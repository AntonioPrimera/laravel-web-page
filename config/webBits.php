<?php

return [
	//--- Definitions -------------------------------------------------------------------------------------------------
	
	'ShortText' => [
		//todo: add model class and admin livewire component
		//'model'	   => '\\App\\Models\\WebBits\\ShortText',					//example
		//'admin'	   => 'App\\Http\\Livewire\\AdminPanel\\Bits\\ShortText',	//example
	],
	
	'LongText' => [
	],
	
	'File' => [
		//todo: add model class
	],
	
	'Source' => [
		'editor' => 'input:file',
		'rules' => ['image'],
	],
	
	//--- Aliases -----------------------------------------------------------------------------------------------------
	
	//short text aliases
	'Label' => 'ShortText',
	'Title' => 'ShortText',
	'Url'   => 'ShortText',
	
	//long text aliases
	'Text'  => 'LongText',
	'Description' => 'LongText',
	
	//file aliases
	'Src' => 'Source',
];