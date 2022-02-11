<?php

namespace AntonioPrimera\WebPage\Definitions;

/**
 * @method array getDefinition(string $type)
 */
class ComponentDictionary extends Dictionary
{
	protected function setup()
	{
		$this->loadDictionaryFromConfig('webComponents.components');
	}
	
	public function defaultDefinition(): array
	{
		return [
			'components' => [],
			'bits' => [],
			'editor' => null,
		];
	}
}