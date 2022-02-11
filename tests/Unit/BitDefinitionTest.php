<?php
namespace AntonioPrimera\WebPage\Tests\Unit;

use AntonioPrimera\Testing\CustomAssertions;
use AntonioPrimera\WebPage\Definitions\BitDefinition;
use AntonioPrimera\WebPage\Tests\TestCase;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class BitDefinitionTest extends TestCase
{
	use CustomAssertions;
	
	/** @test */
	public function a_bit_definition_can_merge_rules_intelligently()
	{
		$definition = new BitDefinition();
		$definition->setRules([
			'string',
			'max:250',
			'min:10',
			Rule::in(['12', '13']),
			Rule::unique('users')
		]);
		
		$definition->setRules([
			'max:25',
			Rule::unique('discs'),
		]);
		
		$this->assertListsEqual(
			['string', 'max:25', 'min:10', Rule::in(['12', '13']), Rule::unique('discs')],
			$definition->getRules()
		);
		
		//make sure the original unique rule was overwritten by the new unique rule
		$uniqueRule = collect($definition->getRules())
			->filter(function($rule) { return $rule instanceof Unique;})
			->first();
		
		$this->assertEquals((string) Rule::unique('discs'), (string) $uniqueRule);
	}
	
	///** @test */
	//public function it_can_create_a_bit_definition_from_a_string()
	//{
	//	$rawDefinition = 'rules:string,url|optional|rule-min:5|rule-max:10|editor:input#text|visible';
	//	$rules = [
	//		'string'   	=> '',
	//		'url'	   	=> '',
	//		'min'	   	=> '5',
	//		'max'	   	=> '10',
	//		'nullable' 	=> '',
	//	];
	//	$definition = [
	//		'editor'  => 'input#text',
	//		'visible' => true,
	//	];
	//	$resolution = [
	//		'rules' 	=> ['string', 'url', 'min:5', 'max:10', 'nullable'],
	//		'editor' 	=> 'input#text',
	//		'visible' 	=> true,
	//	];
	//
	//	$this->assertBitDefinition($rawDefinition, $definition, $rules, $resolution);
	//}
	//
	///** @test */
	//public function it_can_create_a_bit_definition_from_an_array()
	//{
	//	$rawDefinition = [
	//		'rules' => ['string', 'url', 'min:5', 'max:10'],
	//		'optional',
	//		'editor' => 'input#text',
	//		'visible',
	//	];	//'rules:string,url|optional|rule-min:5|rule-max:10|editor:input#text|visible';
	//
	//	$rules = [
	//		'string'   	=> '',
	//		'url'	   	=> '',
	//		'min'	   	=> '5',
	//		'max'	   	=> '10',
	//		'nullable' 	=> '',
	//	];
	//	$definition = [
	//		'editor'  => 'input#text',
	//		'visible' => true,
	//	];
	//	$resolution = [
	//		'rules' 	=> ['string', 'url', 'min:5', 'max:10', 'nullable'],
	//		'editor' 	=> 'input#text',
	//		'visible' 	=> true,
	//	];
	//
	//	$this->assertBitDefinition($rawDefinition, $definition, $rules, $resolution);
	//}
	//
	///** @test */
	//public function it_can_resolve_a_shallow_alias()
	//{
	//	config(['webComponents.bits' => [
	//		'ShortText' => [
	//			'editor'   => 'input#text',
	//			'visible',
	//			'rules'    => ['string', 'max:255', 'border:1px'],
	//		],
	//		'Label' => 'alias:ShortText',
	//	]]);
	//
	//	$rawDefinition = 'alias:ShortText|rules:url|optional|rule-min:5|rule-max:10|editor:pdf|visible';
	//	$rules = [
	//		'string'   	=> '',		//from alias
	//		'url'	   	=> '',
	//		'min'	   	=> '5',
	//		'max'	   	=> '10',
	//		'border'	=> '1px',
	//		'nullable' 	=> '',		//from implicit rule: "optional"
	//	];
	//	$definition = [
	//		'editor'  => 'pdf',
	//		'visible' => true,
	//	];
	//	$resolution = [
	//		'rules' 	=> ['string', 'url', 'min:5', 'max:10', 'nullable', 'border:1px'],
	//		'editor' 	=> 'pdf',
	//		'visible' 	=> true,
	//	];
	//
	//	$this->assertBitDefinition($rawDefinition, $definition, $rules, $resolution);
	//}
	//
	///** @test */
	//public function it_can_resolve_a_deep_alias()
	//{
	//	config(['webComponents.bits' => [
	//		'ShortText' => [
	//			'editor'   => 'input#text',
	//			'visible',
	//			'rules'    => ['string', 'max:255'],
	//		],
	//		'Label'   => 'alias:ShortText|color:red|font:bold|rule-margin:1rem|rule-rating:3stars',
	//		'Sticker' => 'alias:Label|color:pink|position:top-left',
	//		'Note'    => 'alias:Sticker|position:bottom-right|size:big',
	//	]]);
	//
	//	$rawDefinition = 'alias:Note|rules:required,url|color:green|border:1px|rule-rating:4stars';
	//
	//	//$rawDefinition = 'alias:ShortText|rules:url|optional|rule-min:5|rule-max:10|editor:pdf|visible';
	//	$rules = [
	//		//from definition
	//		'required'	=> '',
	//		'url'	   	=> '',
	//		'rating'	=> '4stars',
	//
	//		//from Label
	//		'margin'	=> '1rem',
	//
	//		//from ShortText
	//		'string'   	=> '',		//from alias
	//		'max'	   	=> '255',
	//	];
	//
	//	$definition = [
	//		//from definition
	//		'color'		=> 'green',
	//		'border'	=> '1px',
	//
	//		//from Note
	//		'position' 	=> 'bottom-right',
	//		'size'		=> 'big',
	//
	//		//from Label
	//		'font'		=> 'bold',
	//
	//		//from ShortText
	//		'editor'  => 'input#text',
	//		'visible' => true,
	//	];
	//
	//	$resolution = [
	//		'rules' 	=> ['required', 'url', 'rating:4stars', 'margin:1rem', 'string', 'max:255'],
	//		'color'		=> 'green',
	//		'border'	=> '1px',
	//		'position' 	=> 'bottom-right',
	//		'size'		=> 'big',
	//		'font'		=> 'bold',
	//		'editor'  => 'input#text',
	//		'visible' => true,
	//	];
	//
	//	$this->assertBitDefinition($rawDefinition, $definition, $rules, $resolution);
	//}
	//
	///** @test */
	//public function it_can_create_a_bit_definition_instance_via_static_factory()
	//{
	//	config(['webComponents.bits' => [
	//		'ShortText' => [
	//			'editor'   => 'input#text',
	//			'visible',
	//			'rules'    => ['string', 'max:255'],
	//		],
	//		'Label'   => 'alias:ShortText|color:red|font:bold|rule-margin:1rem|rule-rating:3stars',
	//		'Sticker' => 'alias:Label|color:pink|position:top-left',
	//		'Note'    => 'alias:Sticker|position:bottom-right|size:big',
	//	]]);
	//
	//	$rules = [
	//		//from Label
	//		'margin'	=> '1rem',
	//		'rating'	=> '3stars',
	//
	//		//from ShortText
	//		'string'   	=> '',		//from alias
	//		'max'	   	=> '255',
	//	];
	//
	//	$definition = [
	//		//from Note
	//		'position' 	=> 'bottom-right',
	//		'size'		=> 'big',
	//
	//		//from Sticker
	//		'color'		=> 'pink',
	//
	//		//from Label
	//		'font'		=> 'bold',
	//
	//		//from ShortText
	//		'editor'  => 'input#text',
	//		'visible' => true,
	//	];
	//
	//	$resolution = [
	//		'rules' 	=> ['rating:3stars', 'margin:1rem', 'string', 'max:255'],
	//		'color'		=> 'pink',
	//		'position' 	=> 'bottom-right',
	//		'size'		=> 'big',
	//		'font'		=> 'bold',
	//		'editor'  => 'input#text',
	//		'visible' => true,
	//	];
	//
	//	$bitDefinition = BitDefinition::createFromType('Note');
	//
	//	$this->assertListsEqual($rules, $bitDefinition->getRules());
	//	$this->assertListsEqual($definition, $bitDefinition->getDefinition());
	//	$this->assertListsEqual($resolution, $bitDefinition->toArray());
	//}
	//
	///** @test */
	//public function static_factory_creates_a_default_bit_if_the_given_type_is_not_defined_in_the_config()
	//{
	//	config(['webComponents' => [
	//		'bits' => [
	//			'ShortText' => [
	//				'editor'   => 'input#text',
	//				'visible',
	//				'rules'    => ['string', 'max:255'],
	//			],
	//		],
	//	]]);
	//
	//	$rules = [
	//		'string'	=> '',
	//		'max'	   	=> '255',
	//	];
	//
	//	$definition = [
	//		//from ShortText
	//		'editor'  => 'input#text',
	//		'visible' => true,
	//	];
	//
	//	$resolution = [
	//		'rules' 	=> ['string', 'max:255'],
	//		'editor'  => 'input#text',
	//		'visible' => true,
	//	];
	//
	//	$bitDefinition = BitDefinition::createFromType('SomeUndefinedType');
	//
	//	$this->assertListsEqual($rules, $bitDefinition->getRules());
	//	$this->assertListsEqual($definition, $bitDefinition->getDefinition());
	//	$this->assertListsEqual($resolution, $bitDefinition->toArray());
	//}
	
	//--- Protected helpers -------------------------------------------------------------------------------------------
	
	//protected function assertDefinition($definition, array $expectedDefinition)
	//{
	//	$bitDefinition = new BitDefinition($definition);
	//	//dump($expectedDefinition, $bitDefinition->getDefinition());
	//	$this->assertListsEqual($expectedDefinition, $bitDefinition->getDefinition());
	//
	//	return $this;
	//}
	//
	//protected function assertRules($definition, array $expectedRules)
	//{
	//	$bitDefinition = new BitDefinition($definition);
	//	$this->assertListsEqual($expectedRules, $bitDefinition->getRules());
	//
	//	return $this;
	//}
	//
	//protected function assertResolution($definition, array $expectedResolution)
	//{
	//	$bitDefinition = new BitDefinition($definition);
	//	$this->assertListsEqual($expectedResolution, $bitDefinition->toArray());
	//
	//	return $this;
	//}
	//
	//protected function assertBitDefinition($rawDefinition, $definition, $rules, $resolution)
	//{
	//	$this->assertDefinition($rawDefinition, $definition);
	//	$this->assertRules($rawDefinition, $rules);
	//	$this->assertResolution($rawDefinition, $resolution);
	//
	//	return $this;
	//}
}