<?php

namespace AntonioPrimera\WebPage\Traits;

use AntonioPrimera\WebPage\Models\WebComponent;
use AntonioPrimera\WebPage\Models\WebItem;
use Illuminate\Support\Collection;

trait HasComponents
{
	use RetrievesComponents;
	
	protected Collection | null $components = null;
	
	public function getComponents($readFresh = false)
	{
		if (!$this->components || $readFresh)
			$this->components = $this->retrieveComponents($this instanceof WebComponent ? $this->id : null);
		
		return $this->components;
	}
	
	/**
	 * Create a component, given a description and optionally a definition.
	 * The description must have the following format:
	 *    	"<type>:<name>:<uid>"
	 * 	OR
	 * 		['type' => '...', 'name' => '...', 'uid' => '...']
	 *
	 * The name and the uid are optional. If omitted, the type is used to infer them.
	 *
	 * If $onlyDefined is true (by default true), then a component is created
	 * only if the type defined in the webComponents config.
	 */
	public function createComponent(string | array $description, array | null $recipe = []): WebComponent
	{
		//decompose the item description
		['type' => $type, 'name' => $name, 'uid' => $uid] = is_string($description)
			? decomposeWebItemDescription($description)
			: $description;
		
		//only create a new component if it doesn't already exist
		if ($existingComponent = $this->getOwnComponent($uid))
			return $existingComponent;
		
		$componentRecipe = $recipe ?: $this->getComponentDefinition($type);
		$component = $this->createNewComponent(
			$type,
			$name,
			$uid,
			$componentRecipe['model'] ?? WebComponent::class
		);
		
		//if we have a component recipe (either given, or in the dictionary), also create its sub-components and bits
		if ($componentRecipe)
			$component->createContents($componentRecipe);
		
		return $component;
	}
	
	public function resetComponents()
	{
		$this->components = null;
	}
	
	/**
	 * Get a component instance, given a dot separated uid path
	 */
	public function getComponent(string $uidPath): WebComponent | null
	{
		$uids = explode('.', $uidPath, 2);
		$uid = $uids[0];
		$childPath = $uids[1] ?? null;
		
		$component = $this->getOwnComponent($uid);
		
		//if we have a component and a child uid path, follow the uid path, otherwise return whatever component we have
		return $component && $childPath
			? $component->getComponent($childPath)
			: $component;
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
		foreach ($recipe['bits'] ?? [] as $key => $value) {
			$description = is_numeric($key) ? $value : $key;
			$definition = is_numeric($key) ? null : $value;
			
			$this->createBit($description, $definition);
		}
		
	}
	
	//--- Generic protected helpers -----------------------------------------------------------------------------------
	
	/**
	 * Create a new component based on the given data
	 */
	protected function createNewComponent(string $type, string $name, string $uid, string $class): WebComponent
	{
		$componentClass = is_subclass_of($class, WebComponent::class)
			? $class
			: WebComponent::class;
		
		//create a new component of the given component class
		$component = $componentClass::create([
			'parent_id'  => $this instanceof WebComponent ? $this->id : null,
			'class_name' => $componentClass,
			'type' 		 => $type,
			'name' 		 => $name,
			'uid'  		 => $uid,
		]);
		
		//invalidate the loaded component list (will be loaded fresh when needed)
		$this->cacheComponent($component);
		
		return $component;
	}
	
	/**
	 * Get an own component from parent Component / WebPage
	 */
	protected function getOwnComponent(string $uid): WebComponent | null
	{
		//try the cached components first
		if ($this->components && $this->components->has($uid))
			return $this->components->get($uid);
		
		return $this->components && $this->components->has($uid)
			? $this->components->get($uid)
			: $this->retrieveChildComponent($uid);
	}
	
	//--- Retrieve components from DB ---------------------------------------------------------------------------------
	
	protected function retrieveChildComponent($uid)
	{
		$rawComponent = $this->componentsTable()
			->where('parent_id', $this instanceof WebItem ? $this->id : null)
			->where('uid', $uid)
			->first();
		
		if (!$rawComponent)
			return null;
		
		return $this->cacheComponent($this->createWebComponentFromRawData($rawComponent));
	}
	
	protected function retrieveComponents(int | string | null $parentId): Collection
	{
		$rawComponents = $this->componentsTable()
			->where('parent_id', $parentId)
			->get();
		
		return $rawComponents
			->map(fn($rawAttributes) => $this->cacheComponent($this->createWebComponentFromRawData($rawAttributes)));
	}
	
	//--- Caching components ------------------------------------------------------------------------------------------
	
	protected function cacheComponent(?WebComponent $component): WebComponent | null
	{
		if (!$component)
			return null;
		
		if (!$this->components)
			$this->components = collect();
		
		$this->components->put($component->uid, $component);
		
		return $component;
	}
	
	protected function clearCachedComponent(WebComponent|string $component)
	{
		$uid = is_string($component) ? $component : $component->uid;
		unset($this->components[$uid]);
	}
	
	//--- Component definitions ---------------------------------------------------------------------------------------
	
	protected function getComponentDefinition($type)
	{
		$definition = config("webComponents.$type");
		if (!$definition)
			return [];
		
		//string definitions are considered to be aliases, so a recursive call is made to resolve the alias
		return is_string($definition)
			? $this->getComponentDefinition($definition)
			: $definition;
	}
}