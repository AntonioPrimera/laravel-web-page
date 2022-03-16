<?php

namespace AntonioPrimera\WebPage\Models\Components;

use AntonioPrimera\WebPage\Exceptions\WebCollectionException;
use AntonioPrimera\WebPage\Models\WebComponent;
use Illuminate\Support\Str;

class CollectionComponent extends WebComponent
{
	
	/**
	 * The collection item description, just like any
	 * component description: <type>:<name>:<uid>
	 * Only the type is mandatory.
	 */
	public string | null $itemDescription = null;
	
	/**
	 * The component class for the item. If not given, the
	 * generic CollectionItemComponent class is used
	 */
	public string | null $itemModelClass = null;
	
	/**
	 * Create a new collection item, given the item type as a parameter (if item is
	 * defined in the config webCollections.<itemType>) or if the definition is
	 * given as $this->itemDefinition or $this->itemDefinition() result
	 *
	 * @throws WebCollectionException
	 */
	public function createItem(?string $itemDescription = null)
	{
		$description = $itemDescription ?: $this->itemDescription;
		
		if (!$description)
			throw new WebCollectionException('Invalid collection item description');
		
		['type' => $type, 'name' => $name, 'uid' => $uid] = decomposeWebItemDescription($description);
		
		$item = $this->createComponent(
			[
				'type' => $type,
				'name' => $name,
				'uid'  => $uid . '-' . Str::random(8),
			],
			[
				'model' => $this->itemModelClass
					?: config("webCollections.$type.model")
						?: CollectionItemComponent::class
			]
		);
		
		if ($item instanceof CollectionItemComponent)
			$item->setup();
		
		return $item;
	}
}