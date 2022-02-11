<?php

namespace AntonioPrimera\WebPage\Tests\Feature;

use AntonioPrimera\WebPage\Facades\WebPage;
use AntonioPrimera\WebPage\Tests\TestCase;
use AntonioPrimera\WebPage\Tests\Traits\ComponentAssertions;
use AntonioPrimera\WebPage\Tests\Traits\TestContexts;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WebPageTest extends TestCase
{
	use RefreshDatabase, TestContexts, ComponentAssertions;
	
	/** @test */
	public function it_caches_its_root_web_components()
	{
		$this->createSampleCta();
		$this->assertEquals(['cta', 'footer'], webPage()->components->pluck('uid')->toArray());
		$this->assertEquals(
			spl_object_id(webPage()->get('footer')),
			spl_object_id(WebPage::getComponent('footer'))
		);
	}
}