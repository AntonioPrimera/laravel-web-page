<?php

namespace AntonioPrimera\WebPage;

use AntonioPrimera\WebPage\Managers\ComponentManager;

/**
 * @method create(string $type, string $name, ?string $uid = null): Component
 */
class WebPage
{
	protected ComponentManager $componentManager;
	protected ?string $language = null;
	protected ?string $fallbackLanguage = null;
	
	public function __construct()
	{
	}
	
	public function __call(string $name, array $arguments)
	{
		//forward all unhandled calls to the component manager
		return call_user_func_array([$this->componentManager(), $name], $arguments);
	}
	
	protected function componentManager(): ComponentManager
	{
		if (!$this->componentManager)
			$this->componentManager = new ComponentManager();
		
		return $this->componentManager;
	}
	
	//--- Language management -----------------------------------------------------------------------------------------
	
	public function setLanguage(string $language)
	{
		$this->language = $language;
		return $this;
	}
	
	public function setFallbackLanguage(string $language)
	{
		$this->fallbackLanguage = $language;
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getLanguage(): string
	{
		if (!$this->language)
			$this->language = config('app.locale');
		
		return $this->language;
	}
	
	/**
	 * @return string
	 */
	public function getFallbackLanguage(): string
	{
		if (!$this->fallbackLanguage)
			$this->fallbackLanguage = config('app.fallback_locale');
		
		return $this->fallbackLanguage;
	}
}