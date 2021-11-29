<?php

namespace AntonioPrimera\WebPage\Managers\Traits;

use JetBrains\PhpStorm\ArrayShape;

trait ManagerHelpers
{
	
	/**
	 * Decomposes a description string into an indexed array: [type, name, uid],
	 * so this can be easily destructured using list(). The description
	 * format is '<type>:<name>:<uid>' (only <type> is mandatory)
	 *
	 * @param string $description
	 *
	 * @return array
	 */
	protected function itemDescription(string $description): array
	{
		$descriptionParts = explode(':', $description);
		
		return [
			0 => $descriptionParts[0],							//type
			1 => $descriptionParts[1] ?? $descriptionParts[0],	//name
			2 => $descriptionParts[2] ?? null,					//uid
		];
	}
	
	protected function definedComponentTypes()
	{
		return config('webComponents.components', []);
	}
	
	protected function componentTypeIsDefined(string $type)
	{
		return in_array($type, array_keys($this->definedComponentTypes()));
	}
	
	protected function componentDefinition(string $type)
	{
		return config("webComponents.components.$type");
	}
	
	protected function definedBitTypes()
	{
		return config('webComponents.bits', []);
	}
	
	protected function bitTypeIsDefined(string $type)
	{
		return in_array($type, array_keys($this->definedBitTypes()));
	}
}