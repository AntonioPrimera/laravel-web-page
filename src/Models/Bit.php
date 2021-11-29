<?php

namespace AntonioPrimera\WebPage\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * @property BitDefinition $definition
 * @property string $type
 * @property string $name
 * @property string $uid
 * @property array $data
 * @property int $component_id
 *
 * @property Component $component
 */
class Bit extends Model
{
	protected $guarded = [];
	protected $table = 'lwp_bits';
	
	//definition buffer
	protected $definition = [];
	
	//public function __construct(string $type, string $name, ?string $uid = null)
	//{
	//	parent::__construct([
	//		'type' => $type,
	//		'name' => $name,
	//		'uid'  => $uid ?: Str::slug($name),
	//	]);
	//}
	
	//--- Relations ---------------------------------------------------------------------------------------------------
	
	public function component()
	{
		return $this->belongsTo(Component::class, 'component_id', 'id');
	}
	
	//--- Bit Definition ----------------------------------------------------------------------------------------------
	
	public function getDefinitionAttribute()
	{
		//check the buffer - this lazy loads the definition (buffered for the type)
		if (!isset($this->definition[$this->type]))
			$this->definition[$this->type] = BitDefinition::createFromType($this->type);
		
		return $this->definition[$this->type];
	}
	
	//--- Attribute management ----------------------------------------------------------------------------------------
	
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