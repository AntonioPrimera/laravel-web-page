<?php

namespace AntonioPrimera\WebPage\Models;

use AntonioPrimera\WebPage\Http\Livewire\BitAdmin\TextBitAdmin;
use AntonioPrimera\WebPage\Traits\CleansUp;
use AntonioPrimera\WebPage\Traits\HandlesBitData;
use AntonioPrimera\WebPage\Traits\HasParent;
use AntonioPrimera\WebPage\Traits\RetrievesComponents;
use Illuminate\Contracts\Support\Htmlable;

/**
 * @property string        $type
 * @property string        $name
 * @property string        $uid
 * @property array         $data
 * @property int           $component_id
 *
 * @property WebComponent  $component
 */
class WebBit extends WebItem implements Htmlable, \Stringable
{
	use RetrievesComponents, HasParent, CleansUp, HandlesBitData;
	
	protected $guarded = [];
	protected $table = 'lwp_bits';
	protected $casts = [
		'data' => 'array'
	];
	
	//--- Abstract method implementations -----------------------------------------------------------------------------
	
	public function itemPath(): string
	{
		return $this->parent->itemPath() . ':' . $this->uid;
	}
	
	public function getParent(): WebComponent|null
	{
		return $this->retrieveComponent($this->parent_id);
	}
	
	//--- Override WebItem methods ------------------------------------------------------------------------------------
	
	public function getAdminViewData(): array
	{
		return [
			'bit' => $this,
		];
	}
	
	//--- Interface implementation ------------------------------------------------------------------------------------
	
	public function toHtml()
	{
		return $this->getBitData(webPage()->getLanguage(), '');
	}
	
	public function getAdminViewComponent(): string
	{
		return TextBitAdmin::class;
	}
}