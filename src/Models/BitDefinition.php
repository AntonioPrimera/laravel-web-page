<?php

namespace AntonioPrimera\WebPage\Models;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class BitDefinition implements Arrayable
{
	protected array $definition = [];
	protected array $rules = [];
	
	public function __construct($definition)
	{
		//extract a raw definition, as an array
		$this->setupRawDefinitionAndRules($definition);
		
		//transform indexed items (numeric keys) into associative items
		$this->normalizeDefinition();
		
		//extract the rules and remove them from the definition
		$this->normalizeRules();
		
		//resolve any aliases
		$this->resolveAliases();
	}
	
	//--- Getters and setters -----------------------------------------------------------------------------------------
	
	public function getRules(): array
	{
		return $this->rules;
	}
	
	public function getDefinition(): array
	{
		return $this->definition;
	}
	
	//--- Public static methods ---------------------------------------------------------------------------------------
	
	/**
	 * Static factory. Creates a BitDefinition instance,
	 * given the name of a pre-configured bit type.
	 * See config: webComponents.bits
	 *
	 * @param string $type
	 *
	 * @return static
	 */
	public static function createFromType(string $type)
	{
		return new static(static::getConfigDefinition($type) ?: static::getDefaultDefinition());
	}
	
	//--- Protected helpers -------------------------------------------------------------------------------------------
	
	/**
	 * Given an external definition, as a string, an array
	 * or a BitDefinition instance, normalize
	 * it into an associative array.
	 *
	 * @param $definition
	 *
	 * @return $this
	 */
	protected function setupRawDefinitionAndRules($definition)
	{
		if (is_array($definition)) {
			$this->definition = $definition;
			$this->rules = [];
			return $this;
		}
		
		if (is_string($definition)) {
			$this->definition = $this->parseStringDefinition($definition);
			$this->rules = [];
			return $this;
		}
		
		
		if ($definition instanceof BitDefinition) {
			$this->definition = $definition->getDefinition();
			$this->rules = $definition->getRules();
			return $this;
		}
		
		return $this;
	}
	
	protected function normalizeDefinition()
	{
		$definition = [];
		
		foreach ($this->definition as $key => $value) {
			//transform indexed items into associative items (e.g. 'visible' >>> 'visible' => true
			if (is_numeric($key)) {
				$definition[$value] = true;
				continue;
			}
			
			$definition[$key] = $value;
		}
		
		$this->definition = $definition;
		
		return $this;
	}
	
	/**
	 * Resolves an alias from the definition and
	 * merges this definition and rules with
	 * the alias definition and rules
	 *
	 * @return BitDefinition
	 */
	protected function resolveAliases()
	{
		if (!isset($this->definition['alias']))
			return $this;
		
		//create a new alias BitDefinition (this will recursively resolve aliases)
		$alias = static::createFromType($this->definition['alias']);
		unset($this->definition['alias']);
		
		$this->rules = array_merge($alias->getRules(), $this->rules);
		$this->definition = array_merge($alias->getDefinition(), $this->definition);
		
		return $this;
	}
	
	//--- Rule management ---------------------------------------------------------------------------------------------
	
	/**
	 * Extract any rules from a definition into a normalized associative rule array.
	 *
	 * e.g. [
	 * 			'optional'	=> true,
	 * 			'rule-max' 	=> 50,
	 * 			'rules' => ['string', 'min:5']
	 * 		]
	 *		>>>
	 * 		['nullable' => '', 'max' => 50, 'string' => '', 'min' => '5']
	 *
	 * @return $this
	 */
	protected function normalizeRules(): static
	{
		$rules = [];
		$definition = [];
		
		$explicitRules = [
			'required'  => 'required',
			'mandatory' => 'required',
			'optional'  => 'nullable',
		];
		
		foreach ($this->definition as $key => $value) {
			//normalize implicit rules as keys (e.g. 'mandatory' => true >>> 'required' => '')
			if (isset($explicitRules[$key]) && $value) {
				$rules[$explicitRules[$key]] = '';
				continue;
			}
			
			////normalize explicit rules as values with numeric keys (e.g. 'mandatory' >>> 'required' => '')
			//if (is_numeric($key) && isset($explicitRules[$value])) {
			//	$rules[$explicitRules[$value]] = '';
			//	continue;
			//}
			
			//normalize explicit rules (e.g. 'rule-min' => 25 >>> 'min' => 25)
			if (str_starts_with($key, 'rule-')) {
				$rules[str_replace('rule-', '', $key)] = $value;
				continue;
			}
			
			//normalize the rules array (e.g. ['string', 'max:10'] >>> ['string' => '', 'max' => '10'])
			if ($key === 'rules') {
				$rules = array_merge($this->normalizeRulesArray(Arr::wrap($value)), $rules);
				continue;
			}
			
			//everything else goes into the resulting definition
			$definition[$key] = $value;
		}
		
		$this->definition = $definition;
		$this->rules = array_merge($this->rules, $rules);
		
		return $this;
	}
	
	/**
	 * Unpack an indexed array of rules into an associative array of rules
	 *
	 * e.g. ['string', 'max:10'] >>> ['string' => '', 'max' => '10']
	 *
	 * @param array $rules
	 *
	 * @return array
	 */
	protected function normalizeRulesArray(array $rules): array
	{
		$normalizedRules = [];
		
		foreach ($rules as $rule) {
			$parts = explode(':', $rule);
			$normalizedRules[$parts[0]] = $parts[1] ?? '';
		}
		
		return $normalizedRules;
	}
	
	/**
	 * Pack the rules array into an indexed array.
	 *
	 * e.g. ['string' => '', 'max' => 10] >>> ['string', 'max:10']
	 *
	 * @return array
	 */
	protected function packRules(): array
	{
		return collect($this->rules)
			->map(function($value, $key) {
				return $key . ($value ? ':' . $value : '');
			})
			->values()
			->toArray();
	}
	
	//--- Parsing string definitions ----------------------------------------------------------------------------------
	
	/**
	 * Parses a string definition and returns a raw definition.
	 * A raw definition is not final, because it may still
	 * have aliases, single rules, implicit rules etc.
	 *
	 * @param string $definition
	 *
	 * @return array
	 */
	protected function parseStringDefinition(string $definition): array
	{
		return Str::of($definition)
			->explode('|')
			->mapWithKeys(function($item) {
				return $this->parseStringDefinitionItem($item);
			})
			->toArray();
	}
	
	/**
	 * After a string definition is exploded into items,
	 * this method parses each individual item
	 *
	 * e.g. "editor:input" 				>> ['editor' => 'input']
	 * 		"required"     				>> ['required' => true]
	 * 		"rules:string,url,required' >> ['rules' => ['string', 'url', 'required']]
	 *
	 * @param string $item
	 *
	 * @return array|bool[]
	 */
	protected function parseStringDefinitionItem(string $item)
	{
		$parts = explode(':', $item);
		$key = $parts[0];
		$value = $parts[1] ?? true;
		
		//if no value was found, assume it is boolean true (e.g. 'required' >> ['required' => true])
		if ($value === true || $value === 'true')
			return [$key => true];
		
		//replace string 'false' with boolean false
		if ($value === 'false')
			return [$key => false];
		
		//transform comma separated values into arrays
		if (str_contains($value, ','))
			return [$key => explode(',', $value)];
		
		return [$key => $value];
	}
	
	//--- Protected static helpers ------------------------------------------------------------------------------------
	
	/**
	 * Retrieves a bit definition from the config.
	 *
	 * @param $bitType
	 *
	 * @return string|array
	 */
	protected static function getConfigDefinition($bitType)
	{
		return config("webComponents.bits.{$bitType}", []);
	}
	
	protected static function getDefaultDefinition()
	{
		return static::getConfigDefinition(config('webComponents.defaultBit'));
	}
	
	//--- Interface implementation ------------------------------------------------------------------------------------
	
	public function toArray()
	{
		$definition = $this->definition;
		$definition['rules'] = $this->packRules();
		
		return $definition;
	}
}