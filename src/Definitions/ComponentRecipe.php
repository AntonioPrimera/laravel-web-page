<?php

namespace AntonioPrimera\WebPage\Definitions;

use Illuminate\Contracts\Support\Arrayable;

/**
 * The component Recipe is an object containing a list of
 * components and
 *
 * @property array  $components
 * @property array  $bits
 * @property string $editor
 * @property string $type
 */
class ComponentRecipe implements Arrayable
{
	protected array $components = [];
	protected array $bits = [];
	protected ?string $editor = null;
	protected ?string $type = null;
	
	public function __construct(array $definition = [])
	{
		if ($definition)
			$this->setComponents($definition['components'] ?? [])
				->setBits($definition['bits'] ?? [])
				->setEditor($definition['editor'] ?? null)
				->setType($definition['type'] ?? null);
	}
	
	public function __get(string $name)
	{
		$methodName = 'get' . ucfirst($name);
		if (method_exists($this, $methodName))
			return $this->$methodName();
		
		return null;
	}
	
	public function __set(string $name, $value): void
	{
		$methodName = 'set' . ucfirst($name);
		if (method_exists($this, $methodName))
			$this->$methodName($value);
	}
	
	//--- Getters and setters -----------------------------------------------------------------------------------------
	
	public function getComponents()
	{
		return $this->components;
	}
	
	public function setComponents(array $rawComponents)
	{
		$components = collect($rawComponents)
			->mapWithKeys(function($value, $key) {
				$description = is_numeric($key) ? $value : $key;
				$definition = is_numeric($key) ? [] : $value;
				return [
					$description => $definition
				];
			})
			->toArray();
		
		$this->components = array_merge($this->components, $components);
		return $this;
	}
	
	public function getBits()
	{
		return $this->bits;
	}
	
	public function setBits(array $bits)
	{
		$this->bits = array_merge($this->bits, $bits);
		return $this;
	}
	
	public function getEditor()
	{
		return $this->editor;
	}
	
	public function setEditor(?string $editor)
	{
		$this->editor = $editor;
		return $this;
	}
	
	public function getType()
	{
		return $this->type;
	}
	
	public function setType(?string $type)
	{
		$this->type = $type;
		return $this;
	}
	
	//--- Interfaces --------------------------------------------------------------------------------------------------
	
	public function toArray()
	{
		return [
			'components' => $this->getComponents(),
			'bits' 		 => $this->getBits(),
			'editor' 	 => $this->getEditor(),
			'type'	     => $this->getType(),
		];
	}
}