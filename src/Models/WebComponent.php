<?php

namespace AntonioPrimera\WebPage\Models;

use AntonioPrimera\WebPage\Http\Livewire\ComponentAdmin\GenericComponentAdmin;
use AntonioPrimera\WebPage\Http\Livewire\ComponentAdmin\SubComponentAdmin;
use AntonioPrimera\WebPage\Traits\CleansUp;
use AntonioPrimera\WebPage\Traits\HasBits;
use AntonioPrimera\WebPage\Traits\HasComponents;
use AntonioPrimera\WebPage\Traits\HasParent;
use AntonioPrimera\WebPage\Traits\WebHelpers;
use Carbon\Carbon;

/**
 * @property int $id
 *
 * @property string $type
 * @property string $name
 * @property string $uid
 * @property int    $parent_id
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class WebComponent extends WebItem
{
	use HasComponents, HasParent, HasBits, CleansUp, WebHelpers;
	
	protected $guarded = [];
	protected $table = 'lwp_components';
	
	//--- Override WebItem methods ------------------------------------------------------------------------------------
	
	public function getAdminViewComponent(): string
	{
		return $this->parent instanceof WebComponent
			? SubComponentAdmin::class
			: GenericComponentAdmin::class;
	}
	
	public function getAdminViewData(): array
	{
		return [
			'component' => $this,
		];
	}
	
	//--- Abstract method implementations -----------------------------------------------------------------------------
	
	public function itemPath(): string
	{
		return $this->parent instanceof WebItem
			? $this->parent->itemPath() . '.' . $this->uid
			: $this->uid;
	}
}