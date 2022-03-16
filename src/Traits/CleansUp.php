<?php
namespace AntonioPrimera\WebPage\Traits;

use AntonioPrimera\WebPage\Models\WebBit;
use AntonioPrimera\WebPage\Models\WebComponent;

trait CleansUp
{
	use HasBits, HasComponents;
	
	/**
	 * Deletes an item, either given as an instance, or as a string uid path
	 */
	public function remove(WebComponent | WebBit | string | null $item)
	{
		$itemInstance = $item;
		
		if ($item === null)
			$itemInstance = $this;
		elseif (is_string($item))
			$itemInstance = $this->get($item);
		
		if ($itemInstance instanceof WebBit)
			return $this->removeBit($itemInstance);
		
		if ($itemInstance instanceof WebComponent)
			return $this->removeComponent($itemInstance);
		
		return false;
	}
	
	/**
	 * Delete a component instance with all its children.
	 */
	public function removeComponent(WebComponent $component)
	{
		foreach ($component->getBits(true) as $bit) {
			$this->removeBit($bit);
		}
		
		foreach ($component->getComponents(true) as $childComponent) {
			$this->removeComponent($childComponent);
		}
		
		$this->clearCachedComponent($component);
		return $component->delete();
	}
	
	/**
	 * Delete a bit instance
	 */
	public function removeBit(WebBit $bit)
	{
		$this->clearCachedBit($bit);
		return $bit->delete();
	}
}