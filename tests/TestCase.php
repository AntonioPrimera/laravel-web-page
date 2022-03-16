<?php

namespace AntonioPrimera\WebPage\Tests;

use AntonioPrimera\WebPage\WebPageServiceProvider;
use Illuminate\Support\Facades\Artisan;
use Livewire\LivewireServiceProvider;
use Spatie\MediaLibrary\MediaLibraryServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
	protected string $componentsTable = 'lwp_components';
	protected string $bitsTable = 'lwp_bits';
	
	protected function getPackageProviders($app)
	{
		return [
			WebPageServiceProvider::class,
			LivewireServiceProvider::class,
			MediaLibraryServiceProvider::class,
		];
	}
	
	protected function runSpatieMediaLibraryMigrations()
	{
		//manually generate and migrate spatie/media-library migrations
		$migrationPath = __DIR__ . '/TestContext/2022_02_24_123456_create_media_table.php';
		file_put_contents(
			$migrationPath,
			file_get_contents(__DIR__ . '/../vendor/spatie/laravel-medialibrary/database/migrations/create_media_table.php.stub')
		);
		Artisan::call('migrate --realpath --path=' . $migrationPath);
	}
}