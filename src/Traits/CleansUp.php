<?php
namespace AntonioPrimera\WebPage\Traits;

use AntonioPrimera\WebPage\Models\Bit;
use AntonioPrimera\WebPage\Models\WebComponent;

trait CleansUp
{
	use WebHelpers;
	
	/**
	 * Deletes an item, either given as an instance, or as a string uid path
	 *
	 * By default, items are soft-deleted. Use $force = true
	 * to force delete them from the DB.
	 */
	public function remove(WebComponent | Bit | string | null $item, bool $force = false)
	{
		$itemInstance = $item;
		
		if ($item === null)
			$itemInstance = $this;
		elseif (is_string($item))
			$itemInstance = $this->get($item);
		
		if ($itemInstance instanceof Bit)
			return $this->removeBit($itemInstance, $force);
		
		if ($itemInstance instanceof WebComponent)
			return $this->removeComponent($itemInstance, $force);
		
		return false;
	}
	
	/**
	 * Delete a component instance with all its children.
	 *
	 * By default, items are soft-deleted. Use $force = true
	 * to force delete them from the DB.
	 */
	public function removeComponent(WebComponent $component, bool $force = false)
	{
		foreach ($component->getBits(true) as $bit) {
			$this->removeBit($bit, $force);
		}
		
		foreach ($component->getComponents(true) as $childComponent) {
			$this->removeComponent($childComponent, $force);
		}
		
		return $force ? $component->forceDelete() : $component->delete();
	}
	
	/**
	 * Delete a bit instance
	 *
	 * By default, items are soft-deleted. Use $force = true
	 * to force delete them from the DB.
	 */
	public function removeBit(Bit $bit, bool $force = false)
	{
		return $force ? $bit->forceDelete() : $bit->delete();
	}
}