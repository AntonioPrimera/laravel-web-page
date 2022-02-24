<?php

namespace AntonioPrimera\WebPage\Tests\Feature;

use AntonioPrimera\WebPage\Models\Bit;
use AntonioPrimera\WebPage\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CleanupTest extends TestCase
{
	use RefreshDatabase;
	
	protected function setUp(): void
	{
		parent::setUp();
		$this->runSpatieMediaLibraryMigrations();
	}
	
	/** @test */
	public function it_can_soft_delete_a_bit_instance()
	{
		$component = webPage()->createComponent('Page:HomePage');
		$bit = $component->createBit('Label');
		
		$this->assertInstanceOf(Bit::class, $bit);
		
		$component->removeBit($bit);
		$this->assertSoftDeleted($bit);
	}
	
	/** @test */
	public function it_can_force_delete_a_bit_instance()
	{
		$component = webPage()->createComponent('Page:HomePage');
		$bit = $component->createBit('Label');
		
		$this->assertInstanceOf(Bit::class, $bit);
		
		$component->removeBit($bit, true);
		$this->assertDeleted($bit->getTable(), ['uid' => 'label']);
	}
	
	/** @test */
	public function it_can_soft_delete_a_bit_by_uid()
	{
		$component = webPage()->createComponent('Page:HomePage');
		$bit = $component->createBit('Label');
		
		$this->assertInstanceOf(Bit::class, webPage()->get('home-page:label'));
		
		webPage()->remove('home-page:label');
		$this->assertSoftDeleted($bit->getTable(), ['uid' => 'label']);
	}
	
	/** @test */
	public function it_can_force_delete_a_bit_by_uid()
	{
		$component = webPage()->createComponent('Page:HomePage');
		$bit = $component->createBit('Label');
		
		$this->assertInstanceOf(Bit::class, webPage()->get('home-page:label'));
		
		webPage()->remove('home-page:label', true);
		$this->assertDeleted($bit->getTable(), ['uid' => 'label']);
	}
	
	/** @test */
	public function it_can_soft_delete_a_component_instance()
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
		
		$this->assertSoftDeleted($componentTable, ['uid' => 'home-page']);
		$this->assertSoftDeleted($componentTable, ['uid' => 'header']);
		$this->assertSoftDeleted($bitTable, ['uid' => 'label']);
		$this->assertSoftDeleted($bitTable, ['uid' => 'title']);
	}
	
	/** @test */
	public function it_can_force_delete_a_component_instance()
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
		
		webPage()->removeComponent($homePage, true);
		
		$this->assertDatabaseMissing($componentTable, ['uid' => 'home-page']);
		$this->assertDatabaseMissing($componentTable, ['uid' => 'header']);
		$this->assertDatabaseMissing($bitTable, ['uid' => 'label']);
		$this->assertDatabaseMissing($bitTable, ['uid' => 'title']);
	}
	
	/** @test */
	public function it_can_soft_delete_a_component_by_its_uid()
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
		
		$this->assertSoftDeleted($componentTable, ['uid' => 'home-page']);
		$this->assertSoftDeleted($componentTable, ['uid' => 'header']);
		$this->assertSoftDeleted($bitTable, ['uid' => 'label']);
		$this->assertSoftDeleted($bitTable, ['uid' => 'title']);
	}
	
	/** @test */
	public function it_can_force_delete_a_component_by_its_uid()
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
		
		webPage()->remove('home-page', true);
		
		$this->assertDatabaseMissing($componentTable, ['uid' => 'home-page']);
		$this->assertDatabaseMissing($componentTable, ['uid' => 'header']);
		$this->assertDatabaseMissing($bitTable, ['uid' => 'label']);
		$this->assertDatabaseMissing($bitTable, ['uid' => 'title']);
	}
}