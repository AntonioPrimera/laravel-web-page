<?php

namespace AntonioPrimera\WebPage\Facades;

use AntonioPrimera\WebPage\Definitions\BitDefinition;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array defaultDefinition()
 * @method static loadDefinitions(array $definitions)
 * @method static loadAliases(array $aliases)
 * @method static bool isDefined(string $type)
 * @method static BitDefinition getDefinition(string $type)
 */
class BitDictionary extends Facade
{
	protected static function getFacadeAccessor()
	{
		return \AntonioPrimera\WebPage\Definitions\BitDictionary::class;
	}
}