<?php

namespace AntonioPrimera\WebPage;

use AntonioPrimera\WebPage\Traits\CleansUp;
use AntonioPrimera\WebPage\Traits\HasComponents;
use AntonioPrimera\WebPage\Traits\WebHelpers;

/**
 *
 */
class WebPage
{
	use WebHelpers, HasComponents, CleansUp;
	
	protected ?string $language = null;
	protected ?string $fallbackLanguage = null;
	
	public function __construct()
	{
	}
	
	public function getInstance()
	{
		return $this;
	}
	
	//--- Language management -----------------------------------------------------------------------------------------
	
	public function setLanguage(string $language): WebPage
	{
		$this->language = $language;
		return $this;
	}
	
	public function setFallbackLanguage(string $language): WebPage
	{
		$this->fallbackLanguage = $language;
		return $this;
	}
	
	public function getLanguage(): string
	{
		if (!$this->language)
			$this->language = config('app.locale');
		
		return $this->language;
	}
	
	public function getFallbackLanguage(): string
	{
		if (!$this->fallbackLanguage)
			$this->fallbackLanguage = config('app.fallback_locale');
		
		return $this->fallbackLanguage;
	}
}