<?php

namespace AntonioPrimera\WebPage\Tests\Unit;

use AntonioPrimera\Testing\CustomAssertions;
use AntonioPrimera\WebPage\Definitions\ComponentRecipe;
use AntonioPrimera\WebPage\Facades\ComponentDictionary;
use AntonioPrimera\WebPage\Tests\TestCase;
use Illuminate\Support\Facades\App;

class ComponentDictionaryTest extends TestCase
{
	use CustomAssertions;
	
	protected array $definitions = [
		'Button' => [
			'bits' => ['Label', 'Url'],
		],
		
		'Image' => [
			'bits' => ['Src', 'Label', 'Alt'],
		],
		
		'CTA' => [
			'components' => ['Link', 'Image'],
			'bits' => ['Description'],
			'editor' => 'livewire.cta-editor',
		],
	];
	
	protected array $aliases = [
		'MyCTA' => 'CTA',
		'DeepCTA' => 'MyCTA',
		'VeryDeepCTA' => 'DeepCTA',
	];
	
	
	/** @test */
	public function the_dictionary_can_retrieve_the_correct_definition()
	{
		ComponentDictionary::loadDefinitions($this->definitions);
		
		$definition = ComponentDictionary::getDefinition('CTA');
		
		$this->assertIsArray($definition);
		$this->assertEquals(['Link', 'Image'], $definition['components']);
		$this->assertEquals(['Description'], $definition['bits']);
		$this->assertEquals('livewire.cta-editor', $definition['editor']);
	}
	
	/** @test */
	public function the_dictionary_will_return_the_default_definition_for_an_unknown_type()
	{
		$definition = ComponentDictionary::getDefinition('UnknownBitType');
		
		$this->assertIsArray($definition);
		$this->assertArraysEqual(ComponentDictionary::defaultDefinition(), $definition);
	}
	
	/** @test */
	public function it_will_return_the_aliased_definition_for_a_direct_alias()
	{
		ComponentDictionary::loadDefinitions($this->definitions);
		ComponentDictionary::loadAliases($this->aliases);
		
		$definition = ComponentDictionary::getDefinition('MyCTA');
		
		$this->assertIsArray($definition);
		$this->assertArraysEqual($this->definitions['CTA'], $definition);
	}
	
	/** @test */
	public function it_will_return_the_aliased_definition_for_a_recursive_alias()
	{
		ComponentDictionary::loadDefinitions($this->definitions);
		ComponentDictionary::loadAliases($this->aliases);
		
		$definition = ComponentDictionary::getDefinition('VeryDeepCTA');
		
		$this->assertIsArray($definition);
		$this->assertArraysEqual($this->definitions['CTA'], $definition);
	}
	
	///** @test */
	//public function on_setup_it_loads_the_definitions_from_the_web_page_config()
	//{
	//	config(['webComponents.components' => [
	//		'definitions' => $this->definitions,
	//		'aliases' => $this->aliases,
	//	]]);
	//
	//	ComponentDictionary::setup();
	//
	//	$this->assertEquals(['Description'], ComponentDictionary::getDefinition('CTA')->getBits());
	//	$this->assertEquals(
	//		'livewire.cta-editor',
	//		ComponentDictionary::getDefinition('DeepCTA')->getEditor()
	//	);
	//}
	
	/** @test */
	public function it_is_accessible_via_helper_function()
	{
		$this->assertEquals(
			spl_object_id(\componentDictionary()),
			spl_object_id(App::make(\AntonioPrimera\WebPage\Definitions\ComponentDictionary::class))
		);
	}
	
	/** @test */
	public function a_definition_can_be_retrieved_using_a_helper_function()
	{
		ComponentDictionary::loadDefinitions($this->definitions);
		
		$definition = \componentDefinition('CTA');
		
		$this->assertIsArray($definition);
		$this->assertArraysEqual($this->definitions['CTA'], $definition);
	}
}