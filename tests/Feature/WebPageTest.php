<?php

namespace AntonioPrimera\WebPage\Tests\Feature;

use AntonioPrimera\WebPage\Models\WebComponent;
use AntonioPrimera\WebPage\Tests\TestCase;
use AntonioPrimera\WebPage\Tests\Traits\ComponentAssertions;
use AntonioPrimera\WebPage\Tests\Traits\TestContexts;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WebPageTest extends TestCase
{
	use RefreshDatabase, TestContexts, ComponentAssertions;
	
	/** @test */
	public function it_can_retrieve_any_components()
	{
		$this->createSampleCta();
		
		$this->assertInstanceOf(WebComponent::class, webPage()->get('cta'));
		$this->assertInstanceOf(WebComponent::class, webPage()->get('footer'));
		
		$this->assertInstanceOf(WebComponent::class, webPage()->get('cta.background'));
	}
}