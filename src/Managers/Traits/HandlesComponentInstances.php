<?php

namespace AntonioPrimera\WebPage\Managers\Traits;

use AntonioPrimera\WebPage\Models\Component;
use Illuminate\Support\Str;

trait HandlesComponentInstances
{
	use ManagerHelpers;
	
	/**
	 * The cached components
	 * @var array
	 */
	protected array $components = [];
	
	/**
	 * Create a component, given a description and optionally a definition.
	 * The description has the following format:
	 * 	"<type>:<name>:<uid>"
	 *
	 * The name and the uid are optional. If omitted, the type is used to infer them.
	 *
	 * @param string $description
	 * @param array  $definition
	 *
	 * @return Component|null
	 */
	public function createComponent(string $description, array $definition = []): ?Component
	{
		//decompose the item description
		[$type, $name, $uid] = $this->itemDescription($description);
		
		$validUid = $uid ?: Str::kebab($name);
		
		//if the component already exists, just return it
		if ($existingComponent = $this->getOwnComponent($validUid))
			return $existingComponent;
		
		//only defined components types can be created (webComponent.components)
		if (!$this->componentTypeIsDefined($type))
			return null;
		
		$component = $this->createNewComponent($type, $name, $validUid);
		
		//if any definition is given (or configured) we will create the defined children
		$componentDefinition = $definition ?: $this->componentDefinition($type);
		if (!$componentDefinition)
			return $component;
		
		//create any defined child components
		foreach ($componentDefinition['components'] ?? [] as $subType => $subDefinition) {
			$typeDescription = is_numeric($subType) ? $subDefinition : $subType;
			$typeDefinition = is_numeric($subType) ? [] : $subDefinition;
			
			$component->componentManager()->createComponent($typeDescription, $typeDefinition);
		}
		
		//create any defined child bits
		foreach ($componentDefinition['bits'] ?? [] as $bitDescription)
			$component->componentManager()->createBit($bitDescription);
		
		return $component;
	}
	
	/**
	 * Get a component instance, given a simple
	 * uid or a dot separated uid path
	 *
	 * @param string $uidPath
	 *
	 * @return Component|null
	 */
	public function getComponent(string $uidPath): ?Component
	{
		$uids = explode('.', $uidPath, 2);
		$uid = $uids[0];
		$childPath = $uids[1] ?? null;
		
		$component = $this->getOwnComponent($uid);
		
		//if we have a component and a child uid path, follow the uid path, otherwise return whatever component we have
		return $component && $childPath
			? $component->componentManager()->getComponent($childPath)
			: $component;
	}
	
	//--- Protected helpers -------------------------------------------------------------------------------------------
	
	protected function createNewComponent(string $type, string $name, string $uid): Component
	{
		$attributes = compact('type', 'name', 'uid');
		
		return $this->cacheComponent(
			$this->owner instanceof Component
				? $this->owner->components()->create($attributes)
				: Component::create($attributes)
		);
	}
	
	/**
	 * Add the component to the local cache.
	 *
	 * @param Component|null $component
	 *
	 * @return Component|null
	 */
	protected function cacheComponent(?Component $component): ?Component
	{
		if (!$component)
			return null;
		
		$this->components[$component->uid] = $component;
		return $component;
	}
	
	/**
	 * Try to get the component from the
	 * local cache.
	 *
	 * @param string $uid
	 *
	 * @return Component|null
	 */
	protected function getCachedComponent(string $uid): ?Component
	{
		return $this->components[$uid] ?? null;
	}
	
	/**
	 * Try to read the requested component from the Database. This only searches for root components, so, if the owner
	 * of this manager is a Component instance, the search will not be performed. Use getOwnerComponents()
	 * to search for related components. By default, the component's relations are eager loaded.
	 *
	 * @param string      $uid
	 * @param string|null $withRelated
	 *
	 * @return Component|null
	 */
	protected function getDbComponent(string $uid, ?string $withRelated = 'components.bits'): ?Component
	{
		//don't search the DB for child components
		if ($this->owner instanceof Component)
			return null;
		
		//search for root components only
		$query = Component::whereNull('parent_id')->whereUid($uid);
		
		if ($withRelated)
			$query->with('components.bits');
		
		return $this->cacheComponent($query->first());
	}
	
	/**
	 * If the owner of this ComponentManager instance is a
	 * component, try to get the requested component
	 * from its related component collection.
	 *
	 * @param string $uid
	 *
	 * @return Component|null
	 */
	protected function getOwnerComponent(string $uid): ?Component
	{
		return $this->owner instanceof Component
			? $this->cacheComponent($this->owner->components->firstWhere('uid', $uid))
			: null;
	}
	
	/**
	 * Get an own component from the cache / owner / DB, by its uid.
	 *
	 * @param string $uid
	 *
	 * @return Component|null
	 */
	protected function getOwnComponent(string $uid): ?Component
	{
		return $this->getCachedComponent($uid) ?: $this->getOwnerComponent($uid) ?: $this->getDbComponent($uid);
	}
}