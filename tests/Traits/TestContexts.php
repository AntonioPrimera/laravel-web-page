<?php

namespace AntonioPrimera\WebPage\Tests\Traits;

use AntonioPrimera\WebPage\Facades\BitDictionary;
use AntonioPrimera\WebPage\Facades\ComponentDictionary;
use AntonioPrimera\WebPage\Facades\WebPage;
use AntonioPrimera\WebPage\Models\WebComponent;

trait TestContexts
{
	public function createSampleCta()
	{
		config(['webComponents' => [
			'Page' => [],
			'Section' => [],
			
			'Picture' => [
				'bits' => [
					'Image',
					'Label:ShortText',
					'ShortText:Alt',
					'List:AspectRatio', //=> 'alias:List|values:4/3,5/4,16/9',
					'Title',
				],
			],
			
			'Cta' => [
				'components' => [
					'Picture:Background',
					'Picture:Trigger',
				],
				'bits' => [
					'Title',
					'ShortText:Description',
					'LongText:Callout',
				]
			],
			
			'Footer' => [
				'components' => [
					'Link:FaceBook',
					'Link:Instagram',
				],
				
				'bits' => [
					'LongText:Address',
				]
			],
		]]);
		
		config(['webBits' => [
			'ShortText' => [
				'rules'  => ['string'],
				'editor' => 'input#pdf',
			],
			
			'LongText' => [
				'alias' => 'ShortText',
				'editor' => 'textarea',
			],
			
			'List' => [
				'editor' => 'ListView',
			],
			
			'Image' => [
				'rules'  => ['image'],
				'editor' => 'input#file',
			],
			
			'Title' => 'ShortText',
		]]);
		
		webPage()->createComponent('Cta');
		webPage()->createComponent('Footer');
		
		$this->assertDatabaseHas($this->componentsTable, ['uid' => 'cta', 'name' => 'Cta', 'type' => 'Cta']);
		$this->assertDatabaseHas($this->componentsTable, ['uid' => 'footer', 'name' => 'Footer', 'type' => 'Footer']);
		
		$cta = WebPage::get('cta');
		$this->assertInstanceOf(WebComponent::class, $cta);
		$footer = WebPage::get('footer');
		$this->assertInstanceOf(WebComponent::class, $footer);
		
		return $cta;
	}
}