<?php

namespace AntonioPrimera\WebPage\Tests\Traits;

use AntonioPrimera\WebPage\Facades\BitDictionary;
use AntonioPrimera\WebPage\Facades\ComponentDictionary;
use AntonioPrimera\WebPage\Models\WebComponent;

trait TestContexts
{
	public function createSampleCta()
	{
		ComponentDictionary::loadDefinitions([
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
		]);
		
		BitDictionary::loadDefinitions([
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
		]);
		
		BitDictionary::loadAliases([
			'Title' => 'ShortText',
		]);
		
		webPage()->createCta();
		webPage()->createFooter();
		
		$cta = WebComponent::whereUid('cta')->first();
		$this->assertInstanceOf(WebComponent::class, $cta);
		$footer = WebComponent::whereUid('footer')->first();
		$this->assertInstanceOf(WebComponent::class, $footer);
		
		return $cta;
	}
}