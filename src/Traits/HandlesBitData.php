<?php

namespace AntonioPrimera\WebPage\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

trait HandlesBitData
{
	protected string | null $language = null;
	
	public function get(string | null $language = null, string | null $path = null, mixed $default = null)
	{
		return $this->getData($language ?: webPage()->getLanguage(), $path, $default)
			?: $this->getData(webPage()->getFallbackLanguage(), $path ?: '', $default);
	}
	
	public function set(string $language, mixed $value, string | null $path = null, bool $save = true)
	{
		$data = $this->getAttribute('data');
		Arr::set($data, $language . ($path ? ".$path" : ''), $value);
		$this->setAttribute('data', $data);
		
		if ($save)
			$this->save();
		
		//dump($this->fresh());
		//dd(DB::table('lwp_bits')->where('uid', $this->uid)->get());
		
		return $this;
	}
	
	public function language($language)
	{
		$this->language = $language;
		return $this;
	}
	
	//--- Protected helpers -------------------------------------------------------------------------------------------
	
	protected function getData(string $language, string | null $path, mixed $default)
	{
		return Arr::get($this->data, $language . ($path ? ".$path" : ''), $default);
	}
	
	//--- Interface implementation ------------------------------------------------------------------------------------
	
	//public function __toString()
	//{
	//	return $this->get();
	//}
	
	public function toHtml()
	{
		return $this->get();
	}
}