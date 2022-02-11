<?php

namespace AntonioPrimera\WebPage\Definitions;

use AntonioPrimera\WebPage\Exceptions\BitDefinitionException;
use Illuminate\Contracts\Support\Arrayable;

class BitDefinition implements Arrayable
{
	protected array $validationRules = [];
	protected ?string $editor = null;
	
	/**
	 * @throws BitDefinitionException
	 */
	public function __construct(array $definition = [])
	{
		if ($definition)
			$this->setRules($definition['rules'] ?? [])
				->setEditor($definition['editor'] ?? null);
	}
	
	//--- Editor Management -------------------------------------------------------------------------------------------
	
	public function setEditor(string $editor)
	{
		$this->editor = $editor;
		return $this;
	}
	
	public function getEditor(): string | null
	{
		return $this->editor;
	}
	
	//--- Rule management ---------------------------------------------------------------------------------------------
	
	/**
	 * @throws BitDefinitionException
	 */
	public function setRule($validationRule)
	{
		if (is_string($validationRule))
			return $this->_setStringRule($validationRule);
		
		if (is_object($validationRule))
			return $this->_setRuleInstance($validationRule);
		
		throw new BitDefinitionException('Invalid rule format: ' . gettype($validationRule));
	}
	
	/**
	 * @throws BitDefinitionException
	 */
	public function setRules(array $rules)
	{
		foreach ($rules as $rule) {
			$this->setRule($rule);
		}
		
		return $this;
	}
	
	public function getRules()
	{
		return collect($this->validationRules)
			->map(function($value, $key) {
				if (str_starts_with($key, 'class:'))
					return $value;
				
				return $key . ($value ? ":$value" : '');
			})
			->values()
			->toArray();
	}
	
	//--- Protected helpers -------------------------------------------------------------------------------------------
	
	protected function _setStringRule(string $rule)
	{
		$ruleParts = explode(':', $rule);
		$this->validationRules[$ruleParts[0]] = $ruleParts[1] ?? '';
		
		return $this;
	}
	
	protected function _setRuleInstance($rule)
	{
		$this->validationRules['class:' . class_basename($rule)] = $rule;
		
		return $this;
	}
	
	//--- Interface implementation ------------------------------------------------------------------------------------
	
	public function toArray()
	{
		return [
			'rules' => $this->getRules(),
			'editor' => $this->getEditor(),
		];
	}
}