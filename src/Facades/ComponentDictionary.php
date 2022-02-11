<?php

namespace AntonioPrimera\WebPage\Facades;

use AntonioPrimera\WebPage\Definitions\ComponentRecipe;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array defaultDefinition()
 * @method static loadDefinitions(array $definitions)
 * @method static loadAliases(array $aliases)
 * @method static bool isDefined(string $type)
 * @method static array getDefinition(string $type)
 */
class ComponentDictionary extends Facade
{
	protected static function getFacadeAccessor()
	{
		return \AntonioPrimera\WebPage\Definitions\ComponentDictionary::class;
	}
}