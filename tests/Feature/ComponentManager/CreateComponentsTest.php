<?php
namespace AntonioPrimera\WebPage\Tests\Feature\ComponentManager;

use AntonioPrimera\WebPage\Facades\WebPage;
use AntonioPrimera\WebPage\Managers\ComponentManager;
use AntonioPrimera\WebPage\Models\Bit;
use AntonioPrimera\WebPage\Models\Component;
use AntonioPrimera\WebPage\Tests\TestCase;
use AntonioPrimera\WebPage\Tests\TestContext\ComponentManagerTester;
use AntonioPrimera\WebPage\Tests\Traits\CustomAssertions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use function Symfony\Component\String\s;

class CreateComponentsTest extends TestCase
{
	use RefreshDatabase, CustomAssertions;
	
	protected ComponentManagerTester $cm;
	
	protected function setUp(): void
	{
		parent::setUp();
		$this->cm = new ComponentManagerTester();
	}
	
	/** @test */
	public function it_can_create_a_simple_component_using_the_base_creation_method()
	{
		$componentCount = Component::count();
		$component = $this->cm->createComponent('SomeType', 'Some Name', 'some-uid');
		
		$this->assertEquals($componentCount + 1, Component::count());
		$this->assertInstanceOf(Component::class, $component);
		$this->assertEquals('SomeType', $component->type);
		$this->assertEquals('Some Name', $component->name);
		$this->assertEquals('some-uid', $component->uid);
		$this->assertNull($component->parent_id);
		
		$this->assertComponentIsCached($component);
	}
	
	/** @test */
	public function it_can_create_a_predefined_component_using_the_generic_create_method()
	{
		$componentCount = Component::count();
		config(['webComponents.components.PredefComp' => []]);
		$component = $this->cm->create('PredefComp:SomeName:some-name');
		
		$this->assertEquals($componentCount + 1, Component::count());
		$this->assertInstanceOf(Component::class, $component);
		$this->assertEquals('PredefComp', $component->type);
		$this->assertEquals('SomeName', $component->name);
		$this->assertEquals('some-name', $component->uid);
		$this->assertNull($component->parent_id);
		
		$this->assertComponentIsCached($component);
	}
	
	///** @test */
	//public function it_can_create_a_default_bit()
	//{
	//	config([
	//		'webComponents' => [
	//			'defaultBit' => 'ShortText',
	//			'bits' => [
	//				'ShortText' => [
	//					'rules' => ['string'],
	//					'editor' => 'input#text',
	//				],
	//			],
	//		],
	//	]);
	//
	//	$component = $this->cm->createComponent('Type', 'Name');
	//
	//	$componentCount = Component::count();
	//	$bitCount = Bit::count();
	//	$bit = $component->componentManager()->create(':SomeNameDefBit');
	//
	//	$this->assertInstanceOf(Bit::class, $bit);
	//
	//	$this->assertEquals($componentCount, Component::count());
	//	$this->assertEquals($bitCount + 1, Bit::count());
	//	$this->assertEquals(config('webComponents.defaultBit'), $bit->type);
	//	$this->assertEquals('SomeNameDefBit', $bit->name);
	//	$this->assertEquals('some-name-def-bit', $bit->uid);
	//	$this->assertTrue($component->is($bit->component));
	//
	//	$this->assertBitIsCached($component, $bit, 1);
	//
	//	$this->assertArraysAreSame(config('webComponents.bits.ShortText'), $bit->definition->toArray());
	//}
	
	/** @test */
	public function it_can_create_a_predefined_bit()
	{
		config([
			'webComponents' => [
				'defaultBit' => 'ShortText',
				'bits' => [
					'ShortText' => [
						'rules' => ['string'],
						'editor' => 'input#pdf',
					],
					
					'Title' => 'alias:ShortText',
				],
			]
		]);
		
		$component = $this->cm->createComponent('Type', 'Name');
		
		$componentCount = Component::count();
		$bitCount = Bit::count();
		$bit = $component->componentManager()->create('Title:BigTitle:my-title');
		
		$this->assertInstanceOf(Bit::class, $bit);
		
		$this->assertEquals($componentCount, Component::count());
		$this->assertEquals($bitCount + 1, Bit::count());
		
		$this->assertEquals('Title', $bit->type);
		$this->assertEquals('BigTitle', $bit->name);
		$this->assertEquals('my-title', $bit->uid);
		$this->assertTrue($component->is($bit->component));
		
		$this->assertBitIsCached($component, $bit, 1);
		
		$this->assertArraysAreSame(config('webComponents.bits.ShortText'), $bit->definition->toArray());
	}
	
	/** @test */
	public function the_component_manager_of_a_component_creates_components_related_to_the_component_instance()
	{
		$componentCount = Component::count();
		$component = $this->cm->createComponent('SomeType', 'Some Name', 'some-uid');
		
		$this->assertEquals($componentCount + 1, Component::count());
		$this->assertInstanceOf(Component::class, $component);
		
		$subComponent = $component->componentManager()->createComponent('SubType', 'Sub Comp');
		
		$this->assertEquals($componentCount + 2, Component::count());
		
		$this->assertInstanceOf(Component::class, $subComponent);
		$this->assertEquals('SubType', $subComponent->type);
		$this->assertEquals('Sub Comp', $subComponent->name);
		$this->assertEquals('sub-comp', $subComponent->uid);
		$this->assertEquals($component->id, $subComponent->parent_id);
		
		$this->assertComponentIsCached($subComponent, 1, $component->componentManager());
	}
	
	/** @test */
	public function component_instance_forwards_component_creation_calls_to_its_component_manager()
	{
		config([
			'webComponents' => [
				'defaultBit' => 'ShortText',
				'bits' => [
					'ShortText' => [
						'rules' => ['string'],
						'editor' => 'input#pdf',
					],
					
					'Title' => 'alias:ShortText',
				],
				
				'components' => [
					'Page' => [],
					'Section' => [],
				],
			]
		]);
		
		$component = $this->cm->createComponent('SomeType', 'Some Name', 'some-uid');
		$page = $component->createPage();
		
		$component->refresh();
		//check component creation
		$this->assertComponentIsCached($page, 1, $component->componentManager());
		$this->assertEquals($component->id, $page->parent_id);
		$this->assertCount(1, $component->components);
		$this->assertTrue($page->is($component->components->first()));
		$this->assertTrue($page->parent->is($component));
		
		//check component data
		$this->assertEquals('Page', $page->type);
		$this->assertEquals('Page', $page->name);
		$this->assertEquals('page', $page->uid);
		
		$title = $page->createTitle();
		$this->assertBitIsCached($page, $title);
		$this->assertTrue($page->is($title->component));
		$this->assertEquals('Title', $title->type);
		$this->assertEquals('Title', $title->name);
		$this->assertEquals('title', $title->uid);
	}
	
	/** @test */
	public function it_can_handle_fluent_calls()
	{
		//define some pre-defined components
		config([
			'webComponents' => [
				'bits' => [
					'ShortText' => [
						'rules'  => ['string'],
						'editor' => 'input#pdf',
					],
					
					'Title' => 'alias:ShortText',
				],
				
				'components' => [
					'Page' => [],
					'Section' => [],
				],
			]
		]);
		
		$this->cm->createPage('Home')
			->createSection('Header')
				->createTitle();
		
		$homePage = $this->cm->getComponent('home');
		$this->assertInstanceOf(Component::class, $homePage);
		
		$headerSection = $homePage->componentManager()->getComponent('header');
		$this->assertInstanceOf(Component::class, $headerSection);
		
		$title = $headerSection->getBit('title');
		$this->assertInstanceOf(Bit::class, $title);
		$this->assertEquals('Title', $title->type);
		$this->assertEquals('input#pdf', $title->definition->toArray()['editor']);
	}
	
	/** @test */
	public function it_can_retrieve_deep_nested_components_and_bits()
	{
		//define some pre-defined components
		config([
			'webComponents' => [
				'bits' => [
					'ShortText' => [
						'rules'  => ['string'],
						'editor' => 'input#pdf',
					],
					
					'Title' => 'alias:ShortText',
				],
				
				'components' => [
					'Page' => [],
					'Section' => [],
				],
			]
		]);
		
		$this->cm->createPage('Home')
			->createSection('Header')
			->createTitle();
		
		$this->assertTrue($this->cm->getComponent('home')->is($this->cm->get('home')));
		$home = $this->cm->get('home');
		$this->assertTrue($home->getComponent('header')->is($this->cm->get('home.header')));
		$header = $this->cm->get('home.header');
		$this->assertTrue($header->getBit('title')->is($this->cm->get('home.header:title')));
		$title = $this->cm->get('home.header:title');
		/* @var Bit $title */
		$title->setBitData('en', 'English');
		$title->setBitData('de', 'Deutsch');
		
		$this->assertEquals('English', $this->cm->get('home.header:title#en'));
		$this->assertEquals('Deutsch', $this->cm->get('home.header:title#de'));
		
		WebPage::setLanguage('en');
		$this->assertEquals('English', $this->cm->get('home.header:title#'));
		
		//the default language
		WebPage::setLanguage('de');
		$this->assertEquals('Deutsch', $this->cm->get('home.header:title#'));
		
		//fallback language
		WebPage::setLanguage('it');
		$this->assertEquals('English', $this->cm->get('home.header:title#'));
		
		//fallback language
		$this->assertEquals('English', $this->cm->get('home.header:title#es'));
	}
	
	/** @test */
	public function a_component_creation_will_recursively_create_defined_sub_components_and_bits()
	{
		//define some pre-defined components
		config([
			'webComponents' => [
				'defaultBit' => 'ShortText',
				
				'components' => [
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
				],
				
				'bits' => [
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
					
					'Title' => 'alias:ShortText',
				],
			]
		]);
		
		$cta = $this->cm->createCta();
		$this->assertIsComponent($cta);
		$this->assertComponentDetails($cta, 'Cta', 'Cta', 'cta');
		
		$this->assertHasComponents($cta, ['background', 'trigger']);
		$this->assertHasBits($cta, ['title', 'description', 'callout']);
	}
	
	/** @test */
	public function it_can_handle_recipes()
	{
		config([
			'webComponents' => [
				'defaultBit' => 'ShortText',
				
				'bits' => [
					'ShortText' => [
						'rules'  => ['string'],
						'editor' => 'input',
					],
					
					'Url' => [
						'rule' => ['url'],
						'editor' => 'input',
					],
					
					'LongText' => [
						'rules' => ['string'],
						'editor' => 'textarea',
					],
					
					'Image' => [
						'rules'  => ['image'],
						'editor' => 'input#file',
					],
					
					'Description' => 'alias:LongText',
					
					'Title' => 'alias:ShortText',
				],
				
				'components' => [
					'Page' => [],
					'Section' => [],
					'Picture' => [
						'bits' => [
							'Image',
							'Image:Thumbnail',
							'ShortText:Label'
						],
					],
					
					'Link' => [
						'bits' => [
							'Url:Href',
							'Label',
							'Description'
						],
					],
					
					'Cta' => [
						'components' => [
							'Picture:Background',
						],
						
						'bits' => [
							'Title',
							'ShortText:Description',
						]
					]
				],
			]
		]);
		
		$recipe = [
			'Home' => [				//or Home:Page
				'type' => 'Page',
				
				'components' => [
					'Header' => [	//or Header:Section
						'type' => 'Section',
						
						'bits' => [
							'Title',	//default translation key: home.header.title
							'Description' => 'alias:LongText',	//default translation key: home.header.description
						],
					],
					
					'Tours:Section' => [
						'components' => [
							'Tours:Collection' => [
								'items' => [
									'Tour' => [
										'components' => [
											'Picture',
											'Gallery:Collection' => [
												'items' => [
													'Picture'
												],
											],
										],
										
										'bits' => [
											'Duration'  => 'alias:Number',
											'MinPeople' => 'alias:Number',
											'MaxPeople' => 'alias:Number',
											'Sights'	=> 'alias:List',
											'Description',
										]
									]
								]
							]
						],
						
						'bits' => ['Title', 'Description']
					],
				],
			]
		];
		
		$this->expectNotToPerformAssertions();
	}
	
	//--- Custom assertions -------------------------------------------------------------------------------------------
	
	protected function assertComponentIsCached(Component $component, ?int $cacheCount = null, ?ComponentManager $componentManager = null)
	{
		$cm = $componentManager ?: $this->cm;
		
		$components = (new ComponentManagerTester())->exposeComponents($cm);
		$this->assertIsArray($components);
		
		if (is_numeric($cacheCount))
			$this->assertCount($cacheCount, $components);
		
		$this->assertArrayHasKey($component->uid, $components);
		$this->assertTrue($component->is($components[$component->uid]));
	}
	
	protected function assertBitIsCached(Component $component, Bit $bit, ?int $cacheCount = null, ?ComponentManager $componentManager = null)
	{
		$cm = $componentManager ?: $component->componentManager();
		
		$bits = (new ComponentManagerTester())->exposeBits($cm);
		
		if (is_numeric($cacheCount))
			$this->assertCount($cacheCount, $bits);
		
		$this->assertArrayHasKey($bit->uid, $bits);
		$this->assertTrue($bit->is($bits[$bit->uid]));
	}
	
	//--- Component assertions ----------------------------------------------------------------------------------------
	
	protected function assertIsComponent($component)
	{
		$this->assertInstanceOf(Component::class, $component);
		return $this;
	}
	
	protected function assertHasComponents($component, $expectedComponentUids, $strict = true)
	{
		$this->assertIsComponent($component);
		
		$components = (new ComponentManagerTester())->exposeComponents($component->componentManager());
		$actualComponentUids = collect($components)->pluck('uid')->toArray();
		
		$diff = array_diff(Arr::wrap($expectedComponentUids), $actualComponentUids);
		$this->assertEmpty($diff, 'Following components are missing: ' . implode(', ', $diff));
		
		if ($strict)
			$this->assertCount(count(Arr::wrap($expectedComponentUids)), $actualComponentUids);
		
		return $this;
	}
	
	protected function assertHasBits($component, $expectedBitUids, $strict = true)
	{
		$this->assertIsComponent($component);
		
		$bits = (new ComponentManagerTester())->exposeBits($component->componentManager());
		$actualBitUids = collect($bits)->pluck('uid')->toArray();
		
		$this->assertEmpty(array_diff(Arr::wrap($expectedBitUids), $actualBitUids));
		
		if ($strict)
			$this->assertCount(count(Arr::wrap($expectedBitUids)), $actualBitUids);
		
		
		return $this;
	}
	
	protected function assertComponentDetails($component, $type, $name, $uid)
	{
		$this->assertIsComponent($component);
		$this->assertEquals($type, $component->type);
		$this->assertEquals($name, $component->name);
		$this->assertEquals($uid, $component->uid);
	}
}