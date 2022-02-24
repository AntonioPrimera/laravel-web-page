<?php
namespace AntonioPrimera\WebPage\Tests\Feature;

use AntonioPrimera\Testing\CustomAssertions;
use AntonioPrimera\WebPage\Facades\WebPage;
use AntonioPrimera\WebPage\Models\Bit;
use AntonioPrimera\WebPage\Models\WebComponent;
use AntonioPrimera\WebPage\Tests\TestCase;
use AntonioPrimera\WebPage\Tests\Traits\ComponentAssertions;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateComponentsTest extends TestCase
{
	use RefreshDatabase, CustomAssertions, ComponentAssertions;
	
	//protected function setUp(): void
	//{
	//	parent::setUp();
	//}
	
	/** @test */
	public function it_can_create_a_simple_undefined_component_using_the_base_creation_method()
	{
		$componentCount = WebComponent::count();
		$component = \webPage()->createComponent('SomeType:Some Name:some-uid');
		
		$this->assertEquals($componentCount + 1, WebComponent::count());
		$this->assertIsComponent($component);
		$this->assertComponentDetails($component, 'SomeType', 'Some Name', 'some-uid');
		$this->assertNull($component->parent_id);
	}
	
	/** @test */
	public function it_can_create_a_predefined_component_using_the_base_creation_method()
	{
		$componentCount = WebComponent::count();
		config(['webComponents' => [
			'PredefComp' => [],
		]]);
		
		$component = webPage()->createComponent('PredefComp:SomeName:some-name');
		
		$this->assertEquals($componentCount + 1, WebComponent::count());
		$this->assertIsComponent($component);
		$this->assertComponentDetails($component, 'PredefComp', 'SomeName', 'some-name');
		$this->assertNull($component->parent_id);
	}
	
	/** @test */
	public function it_can_create_a_predefined_bit()
	{
		config(['webBits' => [
			'ShortText' => [
				'rules' => ['string'],
				'editor' => 'input#pdf',
			],
			'Title' => 'ShortText',
		]]);
		
		$component = WebPage::createComponent('Type:Name');
		
		$componentCount = WebComponent::count();
		$bitCount = Bit::count();
		$bit = $component->createBit('Title:BigTitle:my-title');
		
		$this->assertIsBit($bit);
		
		$this->assertEquals($componentCount, WebComponent::count());
		$this->assertEquals($bitCount + 1, Bit::count());
		
		$this->assertBitDetails($bit, 'Title', 'BigTitle', 'my-title');
		$this->assertIsComponent($bit->getParent());
		$this->assertEquals($bit->getParent()->id, $component->id);
		
		$this->assertHasBits($component, ['my-title'], true);
	}
	
	/** @test */
	public function it_can_retrieve_deep_nested_components_and_bits()
	{
		config(['webBits' => [
			'ShortText' => [
				'rules' => ['string'],
				'editor' => 'input#pdf',
			],
			'Title' => 'ShortText',
		]]);
		
		$c = WebPage::createComponent('Page:Home');
		$h = $c->createComponent('Section:Header');
		$h->createBit('Title');
		
		$home = webPage()->get('home');
		$this->assertTrue($home->getComponent('header')->is(webPage()->get('home.header')));
		$header = webPage()->get('home.header');
		$this->assertTrue($header->getBit('title')->is(webPage()->get('home.header:title')));
		$title = webPage()->get('home.header:title');
		/* @var Bit $title */
		$title->setBitData('en', 'English');
		$title->setBitData('de', 'Deutsch');
		
		$this->assertEquals('English', webPage()->get('home.header:title')->getBitData('en'));
		$this->assertEquals('Deutsch', webPage()->get('home.header:title')->getBitData('de'));
		
		WebPage::setLanguage('en');
		$this->assertEquals('English', webPage()->get('home.header:title')->getBitData(null));
		
		//the default language
		WebPage::setLanguage('de');
		$this->assertEquals('Deutsch', webPage()->get('home.header:title')->getBitData(null));
		
		//fallback language
		WebPage::setLanguage('it');
		$this->assertEquals('English', webPage()->get('home.header:title')->getBitData(null));
		
		//fallback language
		$this->assertEquals('English', webPage()->get('home.header:title')->getBitData('es'));
	}
	
	/** @test */
	public function a_component_creation_will_recursively_create_defined_sub_components_and_bits()
	{
		config(['webComponents' => [
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
			'Label' => 'ShortText',
		]]);
		
		$cta = \webPage()->createComponent('Cta')->refresh();
		$this->assertIsComponent($cta);
		$this->assertComponentDetails($cta, 'Cta', 'Cta', 'cta');
		$this->assertHasComponents($cta, ['background', 'trigger']);
		$this->assertHasBits($cta, ['title', 'description', 'callout']);
	}
	
	/** @test */
	public function it_can_handle_recipes()
	{
		
		config(['webComponents' => [
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
			'Label' => 'ShortText',
		]]);
		
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
		
		$this->assertIsComponent($home);
		$this->assertComponentDetails($home, 'Page', 'Home', 'home');
		$this->assertHasComponents($home, ['header', 'tours']);
		$this->assertHasBits($home, ['description', 'page-title']);
		
		$header = $home->getComponent('header');
		$this->assertIsComponent($header);
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
}