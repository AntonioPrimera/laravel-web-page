<?php

namespace AntonioPrimera\WebPage\Definitions;

/**
 * @method array getDefinition(string $type)
 */
class BitDictionary extends Dictionary
{
	const DEFAULT_EDITOR = 'Input';
	const DEFAULT_RULES = [];
	
	protected function setup()
	{
		$this->loadDictionaryFromConfig('webComponents.bits');
	}
	
	public function defaultDefinition(): array
	{
		return [
			'rules' => static::DEFAULT_RULES,
			'editor' => static::DEFAULT_EDITOR,
		];
	}
}