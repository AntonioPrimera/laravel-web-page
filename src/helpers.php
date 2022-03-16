<?php

use AntonioPrimera\WebPage\WebPage;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

function webPage(): WebPage
{
	return App::make(WebPage::class);
}

/**
 * Decomposes a description string into an indexed array: [type, name, uid],
 * so this can be easily destructured using list(). The description
 * format is '<type>:<name>:<uid>' (only <type> is mandatory)
 */
function decomposeWebItemDescription(string $description): array
{
	$descriptionParts = explode(':', $description);
	$type = $descriptionParts[0];
	$name = $descriptionParts[1] ?? $type;
	$uid  = $descriptionParts[2] ?? Str::slug(Str::kebab($name));
	
	return compact('type', 'name', 'uid');
}

function lineBrakesToParagraphs(?string $text, $force = false)
{
	if (!$text)
		return '';
	
	$paragraphs = explode("\n", $text);
	
	//if we only have one paragraph, just return the contents
	if (count($paragraphs) === 1 && !$force)
		return $paragraphs[0];
	
	return implode(array_map(fn($paragraph) => "<p>$paragraph</p>", $paragraphs));
}