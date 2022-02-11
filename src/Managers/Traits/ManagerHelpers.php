<?php

namespace AntonioPrimera\WebPage\Managers\Traits;

use Illuminate\Support\Str;

trait ManagerHelpers
{
	/**
	 * Decomposes a description string into an indexed array: [type, name, uid],
	 * so this can be easily destructured using list(). The description
	 * format is '<type>:<name>:<uid>' (only <type> is mandatory)
	 */
	public function decomposeItemDescription(string $description): array
	{
		$descriptionParts = explode(':', $description);
		$type = $descriptionParts[0];
		$name = $descriptionParts[1] ?? $type;
		$uid  = $descriptionParts[2] ?? Str::slug(Str::kebab($name));
		
		return compact('type', 'name', 'uid');
	}
}