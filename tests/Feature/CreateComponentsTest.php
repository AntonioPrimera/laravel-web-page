<?php
namespace AntonioPrimera\WebPage\Tests\Feature;

use AntonioPrimera\Testing\CustomAssertions;
use AntonioPrimera\WebPage\Facades\BitDictionary;
use AntonioPrimera\WebPage\Facades\ComponentDictionary;
use AntonioPrimera\WebPage\Facades\WebPage;
use AntonioPrimera\WebPage\Managers\ComponentManager;
use AntonioPrimera\WebPage\Models\Bit;
use AntonioPrimera\WebPage\Models\WebComponent;
use AntonioPrimera\WebPage\Tests\TestCase;
use AntonioPrimera\WebPage\Tests\TestContext\ComponentManagerTester;
use AntonioPrimera\WebPage\Tests\Traits\ComponentAssertions;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateComponentsTest extends TestCase
{
	use RefreshDatabase, CustomAssertions, ComponentAssertions;
	
	protected ComponentManagerTester $cm;
	
	protected function setUp(): void
	{
		parent::setUp();
		$this->cm = new ComponentManagerTester();
	}
	
	/** @test */
	public function it_can_create_a_simple_component_using_the_base_creation_method()
	{
		$componentCount = WebComponent::count();
		$component = $this->cm->createComponent('SomeType:Some Name:some-uid');
		
		$this->assertEquals($componentCount + 1, WebComponent::count());
		$this->assertInstanceOf(WebComponent::class, $component);
		$this->assertEquals('SomeType', $component->type);
		$this->assertEquals('Some Name', $component->name);
		$this->assertEquals('some-uid', $component->uid);
		$this->assertNull($component->parent_id);
	}
	
	/** @test */
	public function it_can_create_a_predefined_component_using_the_generic_create_method()
	{
		$componentCount = WebComponent::count();
		ComponentDictionary::loadDefinitions([
			'PredefComp' => [],
		]);
		$component = webPage()->componentManager()->create('PredefComp:SomeName:some-name');
		
		$this->assertEquals($componentCount + 1, WebComponent::count());
		$this->assertInstanceOf(WebComponent::class, $component);
		$this->assertEquals('PredefComp', $component->type);
		$this->assertEquals('SomeName', $component->name);
		$this->assertEquals('some-name', $component->uid);
		$this->assertNull($component->parent_id);
	}
	
	/** @test */
	public function it_can_create_a_predefined_bit()
	{
		BitDictionary::loadDefinitions([
			'ShortText' => [
				'rules' => ['string'],
				'editor' => 'input#pdf',
			],
		]);
		
		BitDictionary::loadAliases([
			'Title' => 'ShortText',
		]);
		
		$component = WebPage::createComponent('Type:Name');
		
		$componentCount = WebComponent::count();
		$bitCount = Bit::count();
		$bit = $component->componentManager()->createBit('Title:BigTitle:my-title');
		
		$this->assertInstanceOf(Bit::class, $bit);
		
		$this->assertEquals($componentCount, WebComponent::count());
		$this->assertEquals($bitCount + 1, Bit::count());
		
		$this->assertEquals('Title', $bit->type);
		$this->assertEquals('BigTitle', $bit->name);
		$this->assertEquals('my-title', $bit->uid);
		$this->assertTrue($component->is($bit->parent));
		
		$this->assertListsEqual(
			BitDictionary::getDefinition('ShortText'),
			$bit->definition
		);
	}
	
	/** @test */
	public function the_component_manager_of_a_component_creates_components_related_to_the_component_instance()
	{
		$componentCount = WebComponent::count();
		$component = $this->cm->createComponent('SomeType:Some Name:some-uid');
		
		$this->assertEquals($componentCount + 1, WebComponent::count());
		$this->assertInstanceOf(WebComponent::class, $component);
		
		$subComponent = $component->componentManager()->createComponent('SubType:Sub Comp');
		
		$this->assertEquals($componentCount + 2, WebComponent::count());
		
		$this->assertInstanceOf(WebComponent::class, $subComponent);
		$this->assertEquals('SubType', $subComponent->type);
		$this->assertEquals('Sub Comp', $subComponent->name);
		$this->assertEquals('sub-comp', $subComponent->uid);
		$this->assertEquals($component->id, $subComponent->parent_id);
	}
	
	/** @test */
	public function component_instance_forwards_component_creation_calls_to_its_component_manager()
	{
		BitDictionary::loadDefinitions([
			'ShortText' => [
				'rules' => ['string'],
				'editor' => 'input#pdf',
			]
		]);
		BitDictionary::loadAliases([
			'Title' => 'ShortText',
		]);
		
		ComponentDictionary::loadDefinitions([
			'Page' => [],
			'Section' => [],
		]);
		
		$component = $this->cm->createComponent('SomeType:Some Name:some-uid');
		$page = $component->createPage();
		
		$component->refresh();
		//check component creation
		$this->assertEquals($component->id, $page->parent_id);
		$this->assertCount(1, $component->components);
		$this->assertTrue($page->is($component->components->first()));
		$this->assertTrue($page->parent->is($component));
		
		//check component data
		$this->assertEquals('Page', $page->type);
		$this->assertEquals('Page', $page->name);
		$this->assertEquals('page', $page->uid);
		
		$title = $page->createTitle();
		$this->assertTrue($page->is($title->parent));
		$this->assertEquals('Title', $title->type);
		$this->assertEquals('Title', $title->name);
		$this->assertEquals('title', $title->uid);
	}
	
	/** @test */
	public function it_can_retrieve_deep_nested_components_and_bits()
	{
		BitDictionary::loadDefinitions([
			'ShortText' => [
				'rules' => ['string'],
				'editor' => 'input#pdf',
			]
		]);
		BitDictionary::loadAliases([
			'Title' => 'ShortText',
		]);
		
		ComponentDictionary::loadDefinitions([
			'Page' => [],
			'Section' => [],
		]);
		
		$c = WebPage::createPage('Home');
		$h = $c->createSection('Header');
		$h->createTitle();
		
		$home = webPage()->get('home');
		$this->assertTrue($home->getComponent('header')->is(webPage()->get('home.header')));
		$header = webPage()->get('home.header');
		$this->assertTrue($header->getBit('title')->is(webPage()->get('home.header:title')));
		$title = webPage()->get('home.header:title');
		/* @var Bit $title */
		$title->setBitData('en', 'English');
		$title->setBitData('de', 'Deutsch');
		
		$this->assertEquals('English', webPage()->get('home.header:title#en'));
		$this->assertEquals('Deutsch', webPage()->get('home.header:title#de'));
		
		WebPage::setLanguage('en');
		$this->assertEquals('English', webPage()->get('home.header:title#'));
		
		//the default language
		WebPage::setLanguage('de');
		$this->assertEquals('Deutsch', webPage()->get('home.header:title#'));
		
		//fallback language
		WebPage::setLanguage('it');
		$this->assertEquals('English', webPage()->get('home.header:title#'));
		
		//fallback language
		$this->assertEquals('English', webPage()->get('home.header:title#es'));
	}
	
	/** @test */
	public function a_component_creation_will_recursively_create_defined_sub_components_and_bits()
	{
		ComponentDictionary::loadDefinitions([
			'Page' => [],
			'Section' => [],
			
			'Picture' => [
				'bits' => [
					'Image',
					'Label',
					'ShortText:Alt',
					'List:AspectRatio', //=> 'alias:List|values:4/3,5/4,16/9',
					'Title',
				],
			],
			
			'Link' => [
				'bits' => [
					'ShortText:Url',
					'Label'
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
			'Label' => 'ShortText',
		]);
		
		$cta = $this->cm->createCta()->refresh();
		$this->assertIsComponent($cta);
		$this->assertComponentDetails($cta, 'Cta', 'Cta', 'cta');
		$this->assertHasComponents($cta, ['background', 'trigger']);
		$this->assertHasBits($cta, ['title', 'description', 'callout']);
	}
	
	/** @test */
	public function it_can_handle_recipes()
	{
		ComponentDictionary::loadDefinitions([
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
		]);
		
		BitDictionary::loadDefinitions([
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
		]);
		
		BitDictionary::loadAliases([
			'Description' => 'LongText',
			'Title' => 'ShortText',
		]);
		
		$recipe = [
			'components' => [
				'Section:Header' => [
					'bits' => [
						'Title',					//default translation key: home.header.title
						'LongText:Description',		//default translation key: home.header.description
					],
				],
				
				'Section:Tours' => [
					'components' => [
						'Collection:Tours' => [
							'components' => [
								'Tour' => [									//there should be only one component
									'components' => [
										'Picture',
										'Collection:Gallery' => [
											'components' => [
												'Picture'
											],
										],
									],
									
									'bits' => [
										'Duration',
										'MinPeople',
										'Number:MaxPeople',
										'List:Sights',
										'Description',
									]
								]
							]
						]
					],
					
					'bits' => ['Title', 'Description']
				],
			],
			
			'bits' => [
				'Description',
				'Title:PageTitle',
			],
		];
		
		$home = webPage()->createComponent('Page:Home', $recipe);
		
		$this->assertInstanceOf(WebComponent::class, $home);
		$this->assertComponentDetails($home, 'Page', 'Home', 'home');
		$this->assertHasComponents($home, ['header', 'tours']);
		$this->assertHasBits($home, ['description', 'page-title']);
		
		$header = $home->getComponent('header');
		$this->assertComponentDetails($header, 'Section', 'Header', 'header');
		$this->assertHasBits($header, ['title', 'description']);
		
		$this->assertBitDetails($header->getBit('title'), 'Title', 'Title', 'title');
		$this->assertBitDetails($header->getBit('description'), 'LongText', 'Description', 'description');
		
		$tours = $home->getComponent('tours');
		$this->assertComponentDetails($tours, 'Section', 'Tours', 'tours');
		$this->assertHasComponents($tours, ['tours']);
		$this->assertHasBits($tours, ['title', 'description']);
		
		$this->assertHasComponents($tours, ['tours']);
		
		$innerTours = $tours->getComponent('tours');
		$this->assertComponentDetails($innerTours, 'Collection', 'Tours', 'tours');
		$this->assertHasComponents($innerTours, ['tour']);
		
		$tour = $innerTours->getComponent('tour');
		$this->assertComponentDetails($tour, 'Tour', 'Tour', 'tour');
		$this->assertHasComponents($tour, ['picture', 'gallery']);
		$this->assertHasBits($tour, ['duration', 'min-people', 'max-people', 'sights', 'description']);
	}
	
	//--- Custom assertions -------------------------------------------------------------------------------------------
	
	//protected function assertComponentIsCached(WebComponent $component, ?int $cacheCount = null, ?ComponentManager $componentManager = null)
	//{
	//	$cm = $componentManager ?: $this->cm;
	//
	//	$components = (new ComponentManagerTester())->exposeComponents($cm);
	//	$this->assertIsArray($components);
	//
	//	if (is_numeric($cacheCount))
	//		$this->assertCount($cacheCount, $components);
	//
	//	$this->assertArrayHasKey($component->uid, $components);
	//	$this->assertTrue($component->is($components[$component->uid]));
	//}
	
	//protected function assertBitIsCached(WebComponent $component, Bit $bit, ?int $cacheCount = null, ?ComponentManager $componentManager = null)
	//{
	//	$cm = $componentManager ?: $component->componentManager();
	//
	//	$bits = (new ComponentManagerTester())->exposeBits($cm);
	//
	//	if (is_numeric($cacheCount))
	//		$this->assertCount($cacheCount, $bits);
	//
	//	$this->assertArrayHasKey($bit->uid, $bits);
	//	$this->assertTrue($bit->is($bits[$bit->uid]));
	//}
}