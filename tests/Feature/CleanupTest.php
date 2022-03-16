<?php

namespace AntonioPrimera\WebPage\Tests\Feature;

use AntonioPrimera\WebPage\Models\WebBit;
use AntonioPrimera\WebPage\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CleanupTest extends TestCase
{
	use RefreshDatabase;
	
	protected function setUp(): void
	{
		parent::setUp();
		//$this->runSpatieMediaLibraryMigrations();
	}
	
	/** @test */
	public function it_can_delete_a_bit_instance()
	{
		$component = webPage()->createComponent('Page:HomePage');
		$bit = $component->createBit('Label');
		
		$this->assertInstanceOf(WebBit::class, $bit);
		
		$component->removeBit($bit);
		$this->assertDeleted($bit);
	}
	
	/** @test */
	public function it_can_delete_a_bit_by_uid()
	{
		$component = webPage()->createComponent('Page:HomePage');
		$bit = $component->createBit('Label');
		
		$this->assertInstanceOf(WebBit::class, webPage()->get('home-page:label'));
		
		webPage()->remove('home-page:label');
		$this->assertDeleted($bit->getTable(), ['uid' => 'label']);
	}
	
	/** @test */
	public function it_can_delete_a_component_instance()
	{
		$homePage = webPage()->createComponent('Page:HomePage');
		$label = $homePage->createBit('Label');
		$header = $homePage->createComponent('Section:Header');
		$title = $header->createBit('Title');
		
		$componentTable = $homePage->getTable();
		$bitTable = $label->getTable();
		
		$this->assertDatabaseHas($componentTable, ['uid' => 'home-page']);
		$this->assertDatabaseHas($componentTable, ['uid' => 'header']);
		$this->assertDatabaseHas($bitTable, ['uid' => 'label']);
		$this->assertDatabaseHas($bitTable, ['uid' => 'title']);
		
		webPage()->removeComponent($homePage);
		
		$this->assertDeleted($componentTable, ['uid' => 'home-page']);
		$this->assertDeleted($componentTable, ['uid' => 'header']);
		$this->assertDeleted($bitTable, ['uid' => 'label']);
		$this->assertDeleted($bitTable, ['uid' => 'title']);
	}
	
	/** @test */
	public function it_can_delete_a_component_by_its_uid()
	{
		$homePage = webPage()->createComponent('Page:HomePage');
		$label = $homePage->createBit('Label');
		$header = $homePage->createComponent('Section:Header');
		$title = $header->createBit('Title');
		
		$componentTable = $homePage->getTable();
		$bitTable = $label->getTable();
		
		$this->assertDatabaseHas($componentTable, ['uid' => 'home-page']);
		$this->assertDatabaseHas($componentTable, ['uid' => 'header']);
		$this->assertDatabaseHas($bitTable, ['uid' => 'label']);
		$this->assertDatabaseHas($bitTable, ['uid' => 'title']);
		
		webPage()->remove('home-page');
		
		$this->assertDeleted($componentTable, ['uid' => 'home-page']);
		$this->assertDeleted($componentTable, ['uid' => 'header']);
		$this->assertDeleted($bitTable, ['uid' => 'label']);
		$this->assertDeleted($bitTable, ['uid' => 'title']);
	}
}