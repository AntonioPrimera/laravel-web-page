<?php

namespace AntonioPrimera\WebPage\Models;

use AntonioPrimera\WebPage\Managers\ComponentManager;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * @property int $id
 *
 * @property string $type
 * @property string $name
 * @property string $uid
 *
 * @property array               $definition
 * @property array               $data
 *
 * @property int                 $parent_id
 *
 * @property Carbon              $created_at
 * @property Carbon              $updated_at
 *
 * @property EloquentCollection  $components
 * @property WebComponent | null $parent
 *
 * @method static WebComponent create(array $attributes)
 *
 * @todo - add component manager methods here (see __call())
 */
class WebComponent extends WebItem
{
	use SoftDeletes;
	
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
		return $this->hasMany(WebComponent::class, 'parent_id', 'id');
	}
	
	public function bits()
	{
		return $this->hasMany(Bit::class, 'parent_id', 'id');
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
}