<?php

namespace AntonioPrimera\WebPage\Tests;

use AntonioPrimera\WebPage\WebPageServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\LivewireServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
	protected function getPackageProviders($app)
	{
		return [
			WebPageServiceProvider::class,
			LivewireServiceProvider::class,
		];
	}
}