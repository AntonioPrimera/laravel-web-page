<?php

namespace AntonioPrimera\WebPage\Tests\Feature;

use AntonioPrimera\WebPage\Models\Bits\ImageBit;
use AntonioPrimera\WebPage\Models\Components\CollectionComponent;
use AntonioPrimera\WebPage\Models\Components\CollectionItemComponent;
use AntonioPrimera\WebPage\Models\WebComponent;
use AntonioPrimera\WebPage\Tests\TestCase;
use AntonioPrimera\WebPage\Tests\Traits\ComponentAssertions;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CollectionsTest extends TestCase
{
	use RefreshDatabase, ComponentAssertions;
	
	/** @test */
	public function it_can_create_an_own_item()
	{
		config([
			'webCollections' => [
				'Tour' => [
					'components' => [
						'Header' => [
							'bits' => [
								'Title:Name',
								'Image:Preview' => ImageBit::class,
							]
						]
					],
					'bits' => [
						'Text:Description',
						'Text:Price',
					],
				],
			],
		]);
		
		$collection = webPage()->createComponent('Collection:Tours', ['model' => CollectionComponent::class]);
		$this->assertInstanceOf(CollectionComponent::class, $collection);
		
		$tourComponents = WebComponent::whereType('Tour')->count();
		
		$tour1 = $collection->createItem('Tour');
		
		$this->assertIsComponent($tour1);
		$this->assertInstanceOf(CollectionItemComponent::class, $tour1);
		$this->assertEquals('Tour', $tour1->type);
		$this->assertEquals('Tour', $tour1->name);
		$this->assertStringStartsWith('tour', $tour1->uid);
		
		$collection->createItem('Tour');
		$collection->createItem('Tour');
		
		$this->assertEquals($tourComponents + 3, WebComponent::whereType('Tour')->count());
	}
}