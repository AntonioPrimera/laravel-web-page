<?php

namespace AntonioPrimera\WebPage\Tests\TestContext;

use AntonioPrimera\WebPage\Managers\ComponentManager;
use JetBrains\PhpStorm\ArrayShape;

class ComponentManagerTester extends \AntonioPrimera\WebPage\Managers\ComponentManager
{
	
	public function _components()
	{
		return $this->components;
	}
	
	//expose the method decomposeMagicCreationMethod
	#[ArrayShape(['type' => "string", 'name' => "string", 'definition' => "array[]"])]
	public function _decomposeMagicCreationMethod($name): ?array
	{
		return $this->decomposeMagicCreationMethod($name);
	}
	
	public function exposeComponents(ComponentManager $componentManager)
	{
		return $componentManager->components;
	}
	
	public function exposeBits(ComponentManager $componentManager)
	{
		return $componentManager->bits;
	}
}