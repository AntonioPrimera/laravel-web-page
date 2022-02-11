<?php

namespace AntonioPrimera\WebPage\Facades;

use AntonioPrimera\WebPage\Managers\ComponentManager;
use AntonioPrimera\WebPage\Models\Bit;
use AntonioPrimera\WebPage\Models\WebComponent;
use Illuminate\Support\Facades\Facade;

/**
 * @method static ComponentManager componentManager()
 * @method static WebComponent | Bit create(string $description)
 * @method static mixed get(string $path, mixed $default = null)
 * @method static WebComponent | null createComponent(string $description, array $definition = [], bool $onlyDefined = false)
 * @method static WebComponent | null getComponent(string $uidPath)
 * @method static Bit createBit(string $description, bool $onlyDefined = false)
 * @method static Bit | null getBit(string $uid)
 *
 * @method static string getLanguage
 * @method static string getFallbackLanguage
 * @method static \AntonioPrimera\WebPage\WebPage setLanguage(string $language)
 * @method static \AntonioPrimera\WebPage\WebPage setFallbackLanguage(string $language)
 */
class WebPage extends Facade
{
	protected static function getFacadeAccessor()
	{
		return \AntonioPrimera\WebPage\WebPage::class;
	}
}