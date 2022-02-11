<?php
namespace AntonioPrimera\WebPage\Managers\Traits;

use AntonioPrimera\WebPage\Models\Bit;
use AntonioPrimera\WebPage\Models\WebComponent;

trait CleansUp
{
	/**
	 * Deletes an item, either given as an instance, or as a string uid path
	 *
	 * By default, items are soft-deleted. Use $force = true
	 * to force delete them from the DB.
	 */
	public function delete(WebComponent | Bit | string $item, bool $force = false)
	{
		$itemInstance = is_string($item) ? $this->get($item) : $item;
		
		if ($itemInstance instanceof Bit)
			return $this->deleteBit($itemInstance, $force);
		
		if ($itemInstance instanceof WebComponent)
			return $this->deleteComponent($itemInstance, $force);
		
		return false;
	}
	
	/**
	 * Delete a component instance with all its children.
	 *
	 * By default, items are soft-deleted. Use $force = true
	 * to force delete them from the DB.
	 */
	public function deleteComponent(WebComponent $component, bool $force = false)
	{
		foreach ($component->bits as $bit) {
			$this->deleteBit($bit, $force);
		}
		
		foreach ($component->components as $childComponent) {
			$this->deleteComponent($childComponent, $force);
		}
		
		return $force ? $component->forceDelete() : $component->delete();
	}
	
	/**
	 * Delete a bit instance
	 *
	 * By default, items are soft-deleted. Use $force = true
	 * to force delete them from the DB.
	 */
	public function deleteBit(Bit $bit, bool $force = false)
	{
		return $force ? $bit->forceDelete() : $bit->delete();
	}
}