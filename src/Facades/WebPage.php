<?php

namespace AntonioPrimera\WebPage\Facades;

use Illuminate\Support\Facades\Facade;

/**
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