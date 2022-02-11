<?php

use AntonioPrimera\WebPage\Definitions\BitDictionary;
use AntonioPrimera\WebPage\Definitions\ComponentDictionary;
use AntonioPrimera\WebPage\WebPage;
use Illuminate\Support\Facades\App;

function webPage(): WebPage
{
	return App::make(WebPage::class);
}

function bitDictionary(): BitDictionary
{
	return App::make(BitDictionary::class);
}

function componentDictionary(): ComponentDictionary
{
	return App::make(ComponentDictionary::class);
}

function bitDefinition($type): array
{
	return bitDictionary()->getDefinition($type);
}

function componentDefinition($type): array
{
	return componentDictionary()->getDefinition($type);
}