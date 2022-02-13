<?php

namespace AntonioPrimera\WebPage\Tests\Feature;

use AntonioPrimera\WebPage\Facades\BitDictionary;
use AntonioPrimera\WebPage\Facades\ComponentDictionary;
use AntonioPrimera\WebPage\Models\WebComponent;
use AntonioPrimera\WebPage\Tests\Traits\TestContexts;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WebItemBaseModelTest extends \AntonioPrimera\WebPage\Tests\TestCase
{
	use RefreshDatabase, TestContexts;
	
	/*
	 * No idea what this test was supposed to do
	 * I will leave it here until I figure it out
	 */
	
	/** @test */
	public function no_assertions()
	{
		$this->markTestIncomplete();
	}
}