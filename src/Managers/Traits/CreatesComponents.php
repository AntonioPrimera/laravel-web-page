<?php

namespace AntonioPrimera\WebPage\Managers\Traits;

use AntonioPrimera\WebPage\Definitions\ComponentRecipe;
use AntonioPrimera\WebPage\Facades\ComponentDictionary;
use AntonioPrimera\WebPage\Models\WebComponent;
use AntonioPrimera\WebPage\WebPage;

trait CreatesComponents
{
	use ManagerHelpers;
	
	/**
	 * Create a component, given a description and optionally a definition.
	 * The description has the following format:
	 *    "<type>:<name>:<uid>"
	 *
	 * The name and the uid are optional. If omitted, the type is used to infer them.
	 *
	 * If $onlyDefined is true (by default true), then a component is created
	 * only if the type defined in the webComponents config.
	 */
	public function createComponent(string $description, array $recipe = []): WebComponent
	{
		//decompose the item description
		['type' => $type, 'name' => $name, 'uid' => $uid] = $this->decomposeItemDescription($description);
		
		//only create a new component if it doesn't already exist
		if ($existingComponent = $this->getOwnComponent($uid))
			return $existingComponent;
		
		$component = $this->createNewComponent($type, $name, $uid);
		
		//if we have a component recipe (either given, or in the dictionary), also create its sub-components and bits
		$componentRecipe = $recipe ?: ComponentDictionary::getDefinition($type);
		if ($componentRecipe)
			$component->componentManager()->createContents($componentRecipe);
		
		return $component;
	}
	
	/**
	 * Given a components and bits recipe, create the sub-components and bits recursively.
	 * The recipe can have a 'components' and/or 'bits', each one containing a set
	 * of components and bits descriptions.
	 */
	public function createContents(array $recipe)
	{
		//create any defined child components
		foreach ($recipe['components'] ?? [] as $key => $value) {
			$description = is_numeric($key) ? $value : $key;
			$subStructure = is_numeric($key) ? [] : $value;
			
			//create the component
			$this->createComponent($description, $subStructure);
		}
		
		//create any defined child bits
		foreach ($recipe['bits'] ?? [] as $description)
			$this->createBit($description);
	}
	
	public function resetComponents()
	{
		if ($this->owner instanceof WebPage)
			$this->owner->resetComponents();
	
		//clear the loaded relations (better than reload them every time, so they are loaded only when needed)
		if ($this->owner instanceof WebComponent)
			$this->owner->unsetRelation('components');
	}
	
	//--- Protected helpers -------------------------------------------------------------------------------------------
	
	protected function createNewComponent(string $type, string $name, string $uid): WebComponent
	{
		$attributes = compact('type', 'name', 'uid');
		
		$component = $this->owner instanceof WebComponent
			? $this->owner->components()->create($attributes)
			: WebComponent::create($attributes);
		
		//invalidate the loaded component list (will be eager loaded fresh when needed)
		$this->resetComponents();
		
		return $component;
	}
}