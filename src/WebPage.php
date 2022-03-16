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
		$this->language = strtolower($language);
		return $this;
	}
	
	public function setFallbackLanguage(string $language): WebPage
	{
		$this->fallbackLanguage = strtolower($language);
		return $this;
	}
	
	public function getLanguage(): string
	{
		if (!$this->language)
			$this->setLanguage(config('app.locale'));
		
		return $this->language;
	}
	
	public function getFallbackLanguage(): string
	{
		if (!$this->fallbackLanguage)
			$this->setFallbackLanguage(config('app.fallback_locale'));
		
		return $this->fallbackLanguage;
	}
	
	/**
	 * Return the list of languages, from the config key 'app.languages'.
	 * e.g.
	 * 	[
	 * 		'en' => [
	 * 			'label' => 'English',
	 * 			...
	 * 		],
	 * 		'es' => [
	 * 			'label' => 'Espanol',
	 * 			...
	 * 		],
	 * 	]
	 */
	public function getLanguages()
	{
		return config('app.languages', [
			$this->language => [],
			$this->fallbackLanguage => [],
		]);
	}
}