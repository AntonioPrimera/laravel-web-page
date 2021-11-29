<?php

return [
	'defaultBit' => 'ShortText',
	
	'components' => [
		'Page' => [
		
		],
		
		'Section' => [
		
		],
		
		'Link' => [
			'bits' => [
				'Label', 'Url'
			]
		],
		
		'Image' => [
			'bits' => [
				'Source', 'Label'
			]
		],
	],
	
	'bits' => [
		'ShortText' => [
			'editor'   => 'input#text',				//default editor, so it can be omitted
			'rules'    => ['string', 'max:255'],
		],
		
		'LongText' => [
			'editor' => 'textarea',
			'rules'  => ['string'],					//default rule, so it can be omitted
		],
		
		//short text aliases
		'Label' => 'alias:ShortText',
		'Title' => 'alias:ShortText|required',
		'Url'   => 'alias:ShortText|rules:url',
		
		//long text aliases
		'Text'  => 'alias:LongText',
		'Description' => 'alias:LongText',
		
		//used for file uploads by the admin
		'File' => [
			'editor' => 'input#file',
		],
		
		//the src attribute for files
		'Source' => 'alias:File|rules:image',
		
		//'Link' => [
		//	'label' => [
		//		'rules' => ['nullable', 'string'],
		//	],
		//
		//	'url' => [
		//		'editor' => 'input:url',
		//		'rules'  => ['required', 'url'],
		//	],
		//
		//	'target' => [
		//		'editor'  => 'select',
		//		'options' => [
		//			'_self','_blank','_parent','_top'
		//		],
		//		'rules'   => ['nullable', 'string', 'in:_self,_blank,_parent,_top'],
		//		'default' => '_self',
		//	],
		//],
		
		//'Image' => [
		//	'url' => [
		//		'editor' => 'input:file',
		//		'rules'  => ['required', 'image'],
		//	],
		//	'label' => [
		//		'rules' => ['nullable', 'string'],
		//	],
		//],
	],
];