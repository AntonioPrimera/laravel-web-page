<?php

namespace AntonioPrimera\WebPage\Tests\Feature;

use AntonioPrimera\WebPage\Models\Bit;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CleanupTest extends \AntonioPrimera\WebPage\Tests\TestCase
{
	use RefreshDatabase;
	
	/** @test */
	public function it_can_soft_delete_a_bit_instance()
	{
		$component = webPage()->createComponent('Page:HomePage');
		$bit = $component->componentManager()->createBit('Label');
		
		$this->assertInstanceOf(Bit::class, $bit);
		
		$component->componentManager()->deleteBit($bit);
		$this->assertSoftDeleted($bit);
	}
	
	/** @test */
	public function it_can_force_delete_a_bit_instance()
	{
		$component = webPage()->createComponent('Page:HomePage');
		$bit = $component->componentManager()->createBit('Label');
		
		$this->assertInstanceOf(Bit::class, $bit);
		
		$component->componentManager()->deleteBit($bit, true);
		$this->assertDeleted($bit->getTable(), ['uid' => 'label']);
	}
	
	/** @test */
	public function it_can_soft_delete_a_bit_by_uid()
	{
		$component = webPage()->createComponent('Page:HomePage');
		$bit = $component->componentManager()->createBit('Label');
		
		$this->assertInstanceOf(Bit::class, webPage()->get('home-page:label'));
		
		webPage()->componentManager()->delete('home-page:label');
		$this->assertSoftDeleted($bit->getTable(), ['uid' => 'label']);
	}
	
	/** @test */
	public function it_can_force_delete_a_bit_by_uid()
	{
		$component = webPage()->createComponent('Page:HomePage');
		$bit = $component->componentManager()->createBit('Label');
		
		$this->assertInstanceOf(Bit::class, webPage()->get('home-page:label'));
		
		webPage()->componentManager()->delete('home-page:label', true);
		$this->assertDeleted($bit->getTable(), ['uid' => 'label']);
	}
	
	/** @test */
	public function it_can_soft_delete_a_component_instance()
	{
		$homePage = webPage()->createComponent('Page:HomePage');
		$label = $homePage->componentManager()->createBit('Label');
		$header = $homePage->componentManager()->createComponent('Section:Header');
		$title = $header->componentManager()->createBit('Title');
		
		$componentTable = $homePage->getTable();
		$bitTable = $label->getTable();
		
		$this->assertDatabaseHas($componentTable, ['uid' => 'home-page']);
		$this->assertDatabaseHas($componentTable, ['uid' => 'header']);
		$this->assertDatabaseHas($bitTable, ['uid' => 'label']);
		$this->assertDatabaseHas($bitTable, ['uid' => 'title']);
		
		webPage()->componentManager()->deleteComponent($homePage);
		
		$this->assertSoftDeleted($componentTable, ['uid' => 'home-page']);
		$this->assertSoftDeleted($componentTable, ['uid' => 'header']);
		$this->assertSoftDeleted($bitTable, ['uid' => 'label']);
		$this->assertSoftDeleted($bitTable, ['uid' => 'title']);
	}
	
	/** @test */
	public function it_can_force_delete_a_component_instance()
	{
		$homePage = webPage()->createComponent('Page:HomePage');
		$label = $homePage->componentManager()->createBit('Label');
		$header = $homePage->componentManager()->createComponent('Section:Header');
		$title = $header->componentManager()->createBit('Title');
		
		$componentTable = $homePage->getTable();
		$bitTable = $label->getTable();
		
		$this->assertDatabaseHas($componentTable, ['uid' => 'home-page']);
		$this->assertDatabaseHas($componentTable, ['uid' => 'header']);
		$this->assertDatabaseHas($bitTable, ['uid' => 'label']);
		$this->assertDatabaseHas($bitTable, ['uid' => 'title']);
		
		webPage()->componentManager()->deleteComponent($homePage, true);
		
		$this->assertDatabaseMissing($componentTable, ['uid' => 'home-page']);
		$this->assertDatabaseMissing($componentTable, ['uid' => 'header']);
		$this->assertDatabaseMissing($bitTable, ['uid' => 'label']);
		$this->assertDatabaseMissing($bitTable, ['uid' => 'title']);
	}
	
	/** @test */
	public function it_can_soft_delete_a_component_by_its_uid()
	{
		$homePage = webPage()->createComponent('Page:HomePage');
		$label = $homePage->componentManager()->createBit('Label');
		$header = $homePage->componentManager()->createComponent('Section:Header');
		$title = $header->componentManager()->createBit('Title');
		
		$componentTable = $homePage->getTable();
		$bitTable = $label->getTable();
		
		$this->assertDatabaseHas($componentTable, ['uid' => 'home-page']);
		$this->assertDatabaseHas($componentTable, ['uid' => 'header']);
		$this->assertDatabaseHas($bitTable, ['uid' => 'label']);
		$this->assertDatabaseHas($bitTable, ['uid' => 'title']);
		
		webPage()->componentManager()->delete('home-page');
		
		$this->assertSoftDeleted($componentTable, ['uid' => 'home-page']);
		$this->assertSoftDeleted($componentTable, ['uid' => 'header']);
		$this->assertSoftDeleted($bitTable, ['uid' => 'label']);
		$this->assertSoftDeleted($bitTable, ['uid' => 'title']);
	}
	
	/** @test */
	public function it_can_force_delete_a_component_by_its_uid()
	{
		$homePage = webPage()->createComponent('Page:HomePage');
		$label = $homePage->componentManager()->createBit('Label');
		$header = $homePage->componentManager()->createComponent('Section:Header');
		$title = $header->componentManager()->createBit('Title');
		
		$componentTable = $homePage->getTable();
		$bitTable = $label->getTable();
		
		$this->assertDatabaseHas($componentTable, ['uid' => 'home-page']);
		$this->assertDatabaseHas($componentTable, ['uid' => 'header']);
		$this->assertDatabaseHas($bitTable, ['uid' => 'label']);
		$this->assertDatabaseHas($bitTable, ['uid' => 'title']);
		
		webPage()->componentManager()->delete('home-page', true);
		
		$this->assertDatabaseMissing($componentTable, ['uid' => 'home-page']);
		$this->assertDatabaseMissing($componentTable, ['uid' => 'header']);
		$this->assertDatabaseMissing($bitTable, ['uid' => 'label']);
		$this->assertDatabaseMissing($bitTable, ['uid' => 'title']);
	}
}