<?php

namespace AntonioPrimera\WebPage\Models\Components;

use AntonioPrimera\WebPage\Exceptions\WebCollectionException;
use AntonioPrimera\WebPage\Models\WebComponent;

class CollectionItemComponent extends WebComponent
{
	
	/**
	 * The collection item description, just like any
	 * component description: <type>:<name>:<uid>
	 * Only the type is mandatory.
	 */
	public string | null $description = null;
	
	/**
	 * The component definition for this item as an array
	 * or the config key for the item definition from
	 * the config "webCollectionItems" as string
	 */
	public array | string $definition = [];
	
	/**
	 * Create the structure of this collection item, given the description parameter
	 *
	 * @throws WebCollectionException
	 */
	public function setup()
	{
		$definition = $this->definition ?: $this->definition() ?: config('webCollections.' . $this->type);
		
		if (!$definition)
			throw new WebCollectionException('Invalid collection item definition');
		
		//create child components and bits
		$this->createContents($definition);
		
		return $this;
	}
	
	public function definition(): array | string
	{
		return [];
	}
}