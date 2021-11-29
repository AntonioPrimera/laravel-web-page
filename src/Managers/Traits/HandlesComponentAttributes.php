<?php
namespace AntonioPrimera\WebPage\Managers\Traits;

use AntonioPrimera\WebPage\Models\Component;
use Illuminate\Support\Arr;

trait HandlesComponentAttributes
{
	
	//todo: remove this;
	//--- Public methods ----------------------------------------------------------------------------------------------
	
	/**
	 * Set the entire definition on the owner component.
	 *
	 * @param string $type
	 * @param array  $fields
	 *
	 * @return $this
	 */
	public function setDefinition(string $type, array $fields)
	{
		$this->setAttribute($this->owner, 'definition', compact('type', 'fields'));
		return $this;
	}
	
	/**
	 * Gets the definition from the owner Component instance.
	 * If $fieldName is given as a parameter, only the
	 * definition of that field is retrieved.
	 *
	 * @param string|null $fieldName
	 *
	 * @return array|null
	 */
	public function getDefinition(?string $fieldName = null): ?array
	{
		return $this->getAttribute($this->owner, $fieldName ? "definition.fields.$fieldName" : 'definition');
	}
	
	//--- Protected methods -------------------------------------------------------------------------------------------
	
	/**
	 * Set a specific attribute on a given component instance
	 *
	 * @param mixed  $component
	 * @param string $attribute
	 * @param mixed  $value
	 * @param bool   $save
	 *
	 * @return mixed
	 */
	protected function setAttribute(mixed $component, string $attribute, mixed $value, bool $save = true)
	{
		if (!$component instanceof Component)
			return null;
		
		return $save
			? $component->update([$attribute => $value])	//persist the change to the DB
			: $component->$attribute = $value;				//only change the transient model
	}
	
	/**
	 * Get a specific attribute from the given Component instance.
	 * If the given attribute is a dot separated path, this
	 * method searches deep in the attribute's array.
	 *
	 * @param Component $component
	 * @param string    $attribute
	 * @param mixed     $default
	 *
	 * @return mixed
	 */
	protected function getAttribute(mixed $component, string $attribute, mixed $default = null)
	{
		if (!$component instanceof Component)
			return $default;
		
		$attributeParts = explode('.', $attribute, 2);
		$attributeName = $attributeParts[0];
		$arrayPath = $attributeParts[1] ?? null;
		
		return $arrayPath
			? Arr::get($component->$attributeName, $arrayPath, $default)
			: ($component->$attributeName ?: $default);
	}
}