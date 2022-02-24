<?php

namespace AntonioPrimera\WebPage\Models;

use AntonioPrimera\WebPage\Traits\CleansUp;
use AntonioPrimera\WebPage\Traits\HasParent;
use AntonioPrimera\WebPage\Traits\RetrievesComponents;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;

/**
 * @property string        $type
 * @property string        $name
 * @property string        $uid
 * @property array         $data
 * @property int           $component_id
 *
 * @property WebComponent  $component
 */
class Bit extends WebItem implements Htmlable
{
	use SoftDeletes, RetrievesComponents, HasParent, CleansUp;
	
	protected $guarded = [];
	protected $table = 'lwp_bits';
	
	//--- Bit data management -----------------------------------------------------------------------------------------
	
	public function setBitData(string $language, mixed $value): static
	{
		Arr::set($this->attributes['data'], strtolower($language), $value);
		return $this;
	}
	
	public function getBitData(?string $language, $default = null): mixed
	{
		return $this->getRawBitData(strtolower($language) ?: webPage()->getLanguage())
			?: $this->getRawBitData(webPage()->getFallbackLanguage(), $default);
	}
	
	//--- Abstract method implementations -----------------------------------------------------------------------------
	
	public function itemPath(): string
	{
		return $this->parent->itemPath() . ':' . $this->uid;
	}
	
	public function getParent(): WebComponent|null
	{
		return $this->retrieveComponent($this->parent_id);
	}
	
	//--- Protected helpers -------------------------------------------------------------------------------------------
	
	protected function getRawBitData(string $language, $default = null)
	{
		return $this->attributes['data'][$language] ?? $default;
	}
	
	//--- Interface implementation ------------------------------------------------------------------------------------
	
	public function toHtml()
	{
		return $this->getBitData(webPage()->getLanguage(), '');
	}
}