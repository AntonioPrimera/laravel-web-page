<?php

return [
	'Page' => [
		//'model' => 'App\\Models\\WebComponents\\Page',
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
	
	'Gallery' => [
		'model' => '\\AntonioPrimera\\WebPage\\Models\\Components\\ImageGalleryComponent'
	],
];