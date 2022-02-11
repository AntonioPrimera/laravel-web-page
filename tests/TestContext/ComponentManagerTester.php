<?php

namespace AntonioPrimera\WebPage\Tests\TestContext;

use AntonioPrimera\WebPage\Managers\ComponentManager;
use JetBrains\PhpStorm\ArrayShape;

class ComponentManagerTester extends \AntonioPrimera\WebPage\Managers\ComponentManager
{
	
	public function exposeComponents(ComponentManager $componentManager)
	{
		return $componentManager->components;
	}
	
	public function exposeBits(ComponentManager $componentManager)
	{
		return $componentManager->bits;
	}
}