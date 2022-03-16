<?php

namespace AntonioPrimera\WebPage\Facades;

use AntonioPrimera\WebPage\Models\WebBit;
use AntonioPrimera\WebPage\Models\WebComponent;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * Trait WebHelpers:
 * @method static mixed get(string $path, mixed $default = null)
 *
 * Trait CleansUp:
 * @method static remove(WebComponent | WebBit | string | null $item)
 * @method static removeComponent(WebComponent $component)
 *
 * Trait HasComponents:
 * @method static Collection getComponents($readFresh = false)
 * @method static WebComponent | null createComponent(string | array $description, array | null $recipe = [])
 * @method static resetComponents
 * @method static WebComponent | null getComponent(string $uidPath)
 * @method static createContents(array $recipe)
 *
 * Own methods (not in Traits):
 * @method static \AntonioPrimera\WebPage\WebPage getInstance
 * @method static string getLanguage
 * @method static string getFallbackLanguage
 * @method static \AntonioPrimera\WebPage\WebPage setLanguage(string $language)
 * @method static \AntonioPrimera\WebPage\WebPage setFallbackLanguage(string $language)
 * @method static array getLanguages
 */
class WebPage extends Facade
{
	protected static function getFacadeAccessor()
	{
		return \AntonioPrimera\WebPage\WebPage::class;
	}
}