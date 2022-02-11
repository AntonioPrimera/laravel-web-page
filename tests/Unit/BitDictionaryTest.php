<?php

namespace AntonioPrimera\WebPage\Tests\Unit;

use AntonioPrimera\Testing\CustomAssertions;
use AntonioPrimera\WebPage\Definitions\BitDefinition;
use AntonioPrimera\WebPage\Facades\BitDictionary;
use AntonioPrimera\WebPage\Tests\TestCase;
use Illuminate\Support\Facades\App;

class BitDictionaryTest extends TestCase
{
	use CustomAssertions;
	
	/** @test */
	public function the_dictionary_can_retrieve_the_correct_definition()
	{
		BitDictionary::loadDefinitions([
			'Text' => [
				'rules' => ['string', 'max:255'],
				'editor' => 'Box',
			],
		]);
		
		$definition = BitDictionary::getDefinition('Text');
		
		$this->assertIsArray($definition);
		$this->assertEquals(['string', 'max:255'], $definition['rules']);
		$this->assertEquals('Box', $definition['editor']);
	}
	
	/** @test */
	public function the_dictionary_will_return_the_default_definition_for_an_unknown_bit_type()
	{
		$definition = BitDictionary::getDefinition('UnknownBitType');
		
		$this->assertIsArray($definition);
		$this->assertArraysEqual(BitDictionary::defaultDefinition(), $definition);
	}
	
	/** @test */
	public function it_will_return_the_aliased_definition_for_a_direct_alias()
	{
		BitDictionary::loadDefinitions([
			'Text' => [
				'rules' => ['string', 'max:255'],
				'editor' => 'Box',
			],
		]);
		
		BitDictionary::loadAliases([
			'St' => 'Text',
		]);
		
		$definition = BitDictionary::getDefinition('St');
		
		$this->assertIsArray($definition);
		$this->assertEquals(['string', 'max:255'], $definition['rules']);
		$this->assertEquals('Box', $definition['editor']);
	}
	
	/** @test */
	public function it_will_return_the_aliased_definition_for_a_recursive_alias()
	{
		BitDictionary::loadDefinitions([
			'Text' => [
				'rules' => ['string', 'max:255'],
				'editor' => 'Box',
			],
		]);
		
		BitDictionary::loadAliases([
			'St' => 'Text',
			'SomeText' => 'St',
			'DeepAlias' => 'SomeText',
			'VeryDeepAlias' => 'DeepAlias',
		]);
		
		$definition = BitDictionary::getDefinition('VeryDeepAlias');
		
		$this->assertIsArray($definition);
		$this->assertEquals(['string', 'max:255'], $definition['rules']);
		$this->assertEquals('Box', $definition['editor']);
	}
	
	/** @test */
	public function it_is_accessible_via_helper_function()
	{
		$this->assertEquals(
			spl_object_id(bitDictionary()),
			spl_object_id(App::make(\AntonioPrimera\WebPage\Definitions\BitDictionary::class))
		);
	}
	
	/** @test */
	public function a_definition_can_be_retrieved_using_a_helper_function()
	{
		$textDefinition = [
			'rules' => ['string', 'max:255'],
			'editor' => 'Box',
		];
		
		BitDictionary::loadDefinitions([
			'Text' => $textDefinition,
		]);
		
		$definition = BitDictionary::getDefinition('Text');
		
		$this->assertIsArray($definition);
		$this->assertArraysEqual($textDefinition, bitDefinition('Text'));
	}
	
	///** @test */
	//public function on_setup_it_loads_the_definitions_from_the_web_page_config()
	//{
	//	config(['webComponents.bits' => [
	//		'definitions' => [
	//			'GeoLocation' => [
	//				'rules' => ['geolocation'],
	//				'editor' => 'livewire.geo-location',
	//			]
	//		],
	//		'aliases' => [
	//			'Pin' => 'GeoLocation',
	//			'Home' => 'Pin',
	//		],
	//	]]);
	//
	//	$this->assertEquals(['geolocation'], BitDictionary::getDefinition('Home')->getRules());
	//	$this->assertEquals('livewire.geo-location', BitDictionary::getDefinition('Pin')->getEditor());
	//}
}