<?php

namespace AntonioPrimera\WebPage;

use AntonioPrimera\WebPage\Exceptions\WebPageException;
use AntonioPrimera\WebPage\Managers\ComponentManager;
use AntonioPrimera\WebPage\Models\Bit;
use AntonioPrimera\WebPage\Models\WebComponent;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

/**
 * @property EloquentCollection $components
 *
 * Creating Components and Bits
 *
 * @method WebComponent | Bit 	create(string $description)
 * @method WebComponent | null 	createComponent(string $description, array $definition = [], bool $onlyDefined = false)
 * @method Bit | null 			createBit(string $description, bool $onlyDefined = false)
 *
 * Retrieving Components, Bits and Bit data
 *
 * @method WebComponent | null 	getComponent(string $uidPath)
 * @method Bit | null 			getBit(string $uid)
 * @method mixed 				get(string $path, mixed $default = null)
 *
 * Getters and setters
 *
 * @method WebPage				getOwner()
 * @method ComponentManager		setOwner(WebPage | WebComponent | null $owner)
 */
class WebPage
{
	protected ?ComponentManager $componentManager = null;
	protected ?string $language = null;
	protected ?string $fallbackLanguage = null;
	protected ?EloquentCollection $components = null;	//emulate an Eloquent model relation
	
	public function __construct()
	{
	}
	
	public function __call(string $name, array $arguments)
	{
		//forward all unhandled calls to the component manager
		return call_user_func_array([$this->componentManager(), $name], $arguments);
	}
	
	/**
	 * @throws WebPageException
	 */
	public function __get(string $name)
	{
		if ($name === 'components')
			return $this->getComponents();
		
		throw new WebPageException("Undefined WebPage property: $name");
	}
	
	public function componentManager(): ComponentManager
	{
		if (!$this->componentManager)
			$this->componentManager = new ComponentManager($this);
		
		return $this->componentManager;
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
	
	//--- Related components ------------------------------------------------------------------------------------------
	
	/**
	 * Get all WebPage WebComponents (root WebComponents)
	 */
	public function getComponents(): EloquentCollection
	{
		if (!$this->components)
			$this->components = WebComponent::whereNull('parent_id')->with('components.bits')->get();
		
		return $this->components;
	}
	
	/**
	 * Resets the components, so they are reloaded when needed. This is
	 * mainly used to invalidate the cached component list, instead
	 * of reloading it on every component creation.
	 */
	public function resetComponents()
	{
		$this->components = null;
	}
}