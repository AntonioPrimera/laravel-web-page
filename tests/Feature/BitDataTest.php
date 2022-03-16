<?php

namespace AntonioPrimera\WebPage\Tests\Feature;

use AntonioPrimera\Testing\CustomAssertions;
use AntonioPrimera\WebPage\Facades\WebPage;
use AntonioPrimera\WebPage\Tests\TestCase;
use AntonioPrimera\WebPage\Tests\Traits\ComponentAssertions;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BitDataTest extends TestCase
{
	use RefreshDatabase, CustomAssertions, ComponentAssertions;
	
	/** @test */
	public function it_returns_null_for_initial_languages()
	{
		$bit = $this->createBit();
		
		$this->assertNull($bit->get());
		$this->assertNull($bit->get('en'));
		$this->assertNull($bit->get('es'));
	}
	
	/** @test */
	public function it_can_save_and_retrieve_simple_language_dependent_strings()
	{
		$bit = $this->createBit();
		
		$bit->set('rand', 'English');
		$this->assertEquals('English', $bit->get('rand'));
		$this->assertNull($bit->get('es'));
		
		$bit->set('rand', 'Random');
		$this->assertEquals('Random', $bit->get('rand'));
		
		$bit->set('es', 'eSpanish');
		$this->assertEquals('Random', $bit->get('rand'));
		$this->assertEquals('eSpanish', $bit->get('es'));
	}
	
	/** @test */
	public function it_will_use_the_fallback_language_for_unset_languages()
	{
		$bit = $this->createBit();
		
		WebPage::setFallbackLanguage('en');
		WebPage::setLanguage('es');
		
		$bit->set('en', 'English');
		$this->assertEquals('English', $bit->get('en'));
		$this->assertEquals('English', $bit->get('es'));
		$this->assertEquals('English', $bit->get('rand'));
	}
	
	/** @test */
	public function it_will_get_the_default_web_page_language_if_no_language_is_given()
	{
		$bit = $this->createBit();
		
		WebPage::setLanguage('es');
		
		$bit->set('es', 'eSpanish');
		$this->assertEquals('eSpanish', $bit->get());
	}
	
	/** @test */
	public function it_can_store_deep_nested_language_dependent_data_given_as_strings()
	{
		$bit = $this->createBit();
		
		WebPage::setLanguage('es');
		
		$bit->set('es', 'webpage.com/spanish', 'url');
		$bit->set('es', 'Spanish link', 'label');
		$bit->refresh();
		
		$this->assertEquals('webpage.com/spanish', $bit->get('es', 'url'));
		$this->assertEquals('Spanish link', $bit->get('es', 'label'));
		$this->assertEquals(['label' => 'Spanish link', 'url' => 'webpage.com/spanish'], $bit->get('es'));
	}
	
	/** @test */
	public function it_can_store_deep_nested_language_dependent_data_given_as_array()
	{
		$bit = $this->createBit();
		
		WebPage::setLanguage('en');
		WebPage::setFallbackLanguage('en');
		
		$data = [
			'image' => '/img/en.jpg',
			'alt' => 'English',
			'meta' => [
				'language' => 'en',
				'location' => 'TestFile'
			]
		];
		$bit->set('en', $data);
		
		$bit->refresh();
	
		//current (populated) language
		$this->assertEquals($data['image'], $bit->get('en', 'image'));
		$this->assertEquals($data['alt'], $bit->get(null, 'alt'));
		$this->assertEquals($data['meta']['language'], $bit->get('en', 'meta.language'));
		$this->assertEquals($data['meta']['location'], $bit->get('en', 'meta.location'));
		$this->assertEquals($data['meta'], $bit->get('en', 'meta'));
		
		//fallback language
		$this->assertEquals($data['image'], $bit->get('es', 'image'));
		$this->assertEquals($data['meta']['language'], $bit->get('fr', 'meta.language'));
	}
	
	protected function createBit()
	{
		$c = WebPage::createComponent('Page:HomePage:home');
		return $c->createBit('ShortText:Bit:bit')->refresh();
	}
}