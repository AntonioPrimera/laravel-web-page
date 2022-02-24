<?php

return [
	'Page' => [
		'class' => 'App\\Models\\WebComponents\\Page',
	],
	
	'Section' => [],
	
	'Link' => [
		'bits' => [
			'Label', 'Url'
		],
	],
	
	'MediaGallery' => [],
	
	'Image' => [
		'bits' => [
			'Source', 'Label'
		],
	],
	
	'Button' => [
		'bits' => [
			'ShortText:Label'
		],
	],
];