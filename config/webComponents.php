<?php

return [
	'components' => [
		'definitions' => [
			'Page' => [],
			
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
		],
		
		'aliases' => [
		
		],
	],
	
	'bits' => [
		'definitions' => [
			'ShortText' => [
				'editor'   => 'input#text',
				'rules'    => ['string', 'max:255'],
			],
			
			'LongText' => [
				'editor' => 'textarea',
				'rules'  => ['string'],
			],
			
			'File' => [
				'editor' => 'input:file',				//todo: change the editor into a livewire component
			],
			
			'Source' => [
				'editor' => 'input:file',
				'rules' => ['image'],
			],
		],
		
		'aliases' => [
			//short text aliases
			'Label' => 'ShortText',
			'Title' => 'ShortText',
			'Url'   => 'ShortText',
			
			//long text aliases
			'Text'  => 'LongText',
			'Description' => 'LongText',
			
			//file aliases
			'Src' => 'Source',
		],
	],
];