<?php

namespace AntonioPrimera\WebPage\Managers\Traits;

use AntonioPrimera\WebPage\Models\WebComponent;
use AntonioPrimera\WebPage\WebPage;

trait RetrievesComponents
{
	use ManagerHelpers;
	
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
			? $component->componentManager()->getComponent($childPath)
			: $component;
	}
	
	//--- Protected helpers -------------------------------------------------------------------------------------------
	
	/**
	 * Get an own component from parent Component / WebPage
	 */
	protected function getOwnComponent(string $uid): WebComponent | null
	{
		//the WebPage emulates the 'components' relation of an Eloquent Model
		return $this->owner instanceof WebComponent || $this->owner instanceof WebPage
			? $this->owner->components->firstWhere('uid', $uid)
			: null;
	}
	
	///**
	// * Try to read the requested component from the Database. This only searches for root components, so, if the owner
	// * of this manager is a Component instance, the search will not be performed. Use getOwnerComponents()
	// * to search for related components. By default, the component's relations are eager loaded.
	// *
	// * @param string      $uid
	// * @param string|null $withRelated
	// *
	// * @return WebComponent|null
	// */
	//protected function getDbComponent(string $uid, ?string $withRelated = 'components.bits'): ?WebComponent
	//{
	//	//don't search the DB for child components
	//	if ($this->owner instanceof WebComponent)
	//		return null;
	//
	//	//search for root components only
	//	$query = WebComponent::whereNull('parent_id')->whereUid($uid);
	//
	//	if ($withRelated)
	//		$query->with('components.bits');
	//
	//	return $query->first();
	//	//return $this->cacheComponent($query->first());
	//}
	//
	///**
	// * If the owner of this ComponentManager instance is a
	// * component, try to get the requested component
	// * from its related component collection.
	// *
	// * @param string $uid
	// *
	// * @return WebComponent|null
	// */
	//protected function getOwnerComponent(string $uid): ?WebComponent
	//{
	//	if ($this->owner instanceof WebComponent)
	//		return $this->owner->components->firstWhere('uid', $uid);
	//
	//	if ($this->owner instanceof WebPage)
	//		return $this->owner->components->firstWhere('uid', $uid);
	//
	//	return null;
	//}
}