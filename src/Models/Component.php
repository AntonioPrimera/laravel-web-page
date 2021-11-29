<?php

namespace AntonioPrimera\WebPage\Models;

use AntonioPrimera\WebPage\Managers\ComponentManager;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property int $id
 *
 * @property string $type
 * @property string $name
 * @property string $uid
 *
 * @property array $definition
 * @property array $data
 *
 * @property int $parent_id
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property EloquentCollection $components
 * @property ?Component $parent
 *
 * @todo - add component manager methods here (see __call())
 */
class Component extends Model
{
	protected $guarded = [];
	protected $table = 'lwp_components';
	protected ?ComponentManager $componentManager = null;
	
	public function __call($method, $parameters)
	{
		//forward all methods starting with "create" (but not "create") to the component manager
		if (Str::of($method)->startsWith('create') && $method !== 'create')
			return $this->forwardCallTo($this->componentManager(), $method, $parameters);
		
		//everything else will be handled by the model magic call handler
		return parent::__call($method, $parameters);
	}
	
	//--- Relations ---------------------------------------------------------------------------------------------------
	
	public function components()
	{
		return $this->hasMany(Component::class, 'parent_id', 'id');
	}
	
	public function bits()
	{
		return $this->hasMany(Bit::class, 'component_id', 'id');
	}
	
	public function parent()
	{
		return $this->belongsTo(Component::class, 'parent_id', 'id');
	}
	
	//--- Relation managers -------------------------------------------------------------------------------------------
	
	/**
	 * Get the own component manager instance. This lazily
	 * instantiates it on the first call.
	 *
	 * @return ComponentManager
	 */
	public function componentManager(): ComponentManager
	{
		if (!$this->componentManager)
			$this->componentManager = new ComponentManager($this);
		
		return $this->componentManager;
	}
	
	public function getComponent(string $uidPath)
	{
		return $this->componentManager()->getComponent($uidPath);
	}
	
	public function getBit(string $uid)
	{
		return $this->componentManager()->getBit($uid);
	}
	
	///**
	// * Create a new child component, forwarding the call to
	// * the attached component manager.
	// *
	// * @param string      $type
	// * @param string      $name
	// * @param string|null $uid
	// *
	// * @return Component
	// */
	//public function createComponent(string $type, string $name, ?string $uid = null): static
	//{
	//	return $this->componentManager()->create($type, $name, $uid);
	//}
	
	//--- Accessors ---------------------------------------------------------------------------------------------------
	
	///**
	// * To get a component, use a path like: "dot.separated.path"
	// * To get a bit from this component use a path like: ":bitName".
	// * To get a bit from a component use a path like: "component.dot.separated.path:bitName"
	// * To get an attribute from a bit, append "#attributeName" to the $uidPath: "component.path:bitName#attributeName"
	// *
	// * @param string      $path
	// * @param string|null $language
	// * @param mixed|null  $default
	// *
	// * @return Component|mixed|null
	// */
	//public function get(string $path, ?string $language = null, mixed $default = null)
	//{
	//	//$path example: 'component.dot.separated.path:bitUid#dataAttributeName'
	//	$pathParts = explode(':', $path, 2);
	//	$componentPath = $pathParts[0];
	//
	//	//search for the targeted component
	//	$component = $this;
	//	foreach (explode('.', $componentPath) as $componentUid) {
	//		$component = $component->components->where('uid', '=', $componentUid)->first();
	//		if (!$component)
	//			return $default;
	//	}
	//
	//	$bitPath = $pathParts[1] ?? null;
	//	if (!$bitPath)
	//		return $component;
	//
	//	$bitPathParts = explode('#', $bitPath, 2);
	//	$bitUid = $bitPathParts[0];
	//	$dataAttribute = $bitPathParts[1] ?? null;
	//
	//	$bit = $component->bits->where('uid', '=', $bitUid)->first();
	//
	//	return $bit && $dataAttribute
	//		? $bit->getBitData($language ?: webPage()->getLanguage(), $dataAttribute, $default)
	//		: $bit;
	//}
}