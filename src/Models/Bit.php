<?php

namespace AntonioPrimera\WebPage\Models;

use AntonioPrimera\WebPage\Definitions\BitDefinition;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;

/**
 * @property BitDefinition $definition
 * @property string        $type
 * @property string        $name
 * @property string        $uid
 * @property array         $data
 * @property int           $component_id
 *
 * @property WebComponent  $component
 */
class Bit extends WebItem
{
	use SoftDeletes;
	
	const IS_LEAF = true;
	
	protected $guarded = [];
	protected $table = 'lwp_bits';
	
	//definition buffer
	protected array | null $definition = null;
	
	//--- Bit Definition ----------------------------------------------------------------------------------------------
	
	public function getDefinitionAttribute()
	{
		//check the buffer - this lazy loads the definition (buffered for the type)
		if (!$this->definition)
			$this->definition = bitDefinition($this->type);
		
		return $this->definition;
	}
	
	//--- Getters and mutators ----------------------------------------------------------------------------------------
	
	public function getValueAttribute()
	{
		return $this->getBitData($this->language);
	}
	
	public function setValueAttribute($value)
	{
		return $this->setBitData($this->language, $value);
	}
	
	//--- Bit data management -----------------------------------------------------------------------------------------
	
	public function setBitData(string $language, mixed $value): static
	{
		Arr::set($this->attributes['data'], strtolower($language), $value);
		return $this;
	}
	
	public function getBitData(?string $language, $default = null): mixed
	{
		//try the given language or the default language
		return ($this->attributes['data'][strtolower($language ?: webPage()->getLanguage())] ?? null)
			?: ($this->attributes['data'][webPage()->getFallbackLanguage()] ?? null)	//try the fallback language
			?: $default;																//return the default value
	}
}