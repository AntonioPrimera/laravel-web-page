<?php

namespace AntonioPrimera\WebPage;

use AntonioPrimera\WebPage\Console\Commands\CreateRecipeCommand;
use AntonioPrimera\WebPage\Console\Commands\RunRecipeCommand;
use AntonioPrimera\WebPage\Http\Livewire\BitAdmin\ImageBitAdmin;
use AntonioPrimera\WebPage\Http\Livewire\BitAdmin\TextBitAdmin;
use AntonioPrimera\WebPage\Http\Livewire\ComponentAdmin\GenericComponentAdmin;
use AntonioPrimera\WebPage\Http\Livewire\ComponentAdmin\ImageGalleryComponentAdmin;
use AntonioPrimera\WebPage\Http\Livewire\ComponentAdmin\SubComponentAdmin;
use Illuminate\Support\ServiceProvider;

use Livewire\Livewire;

class WebPageServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->singleton(WebPage::class, function($app) {
			return new WebPage();
		});
		
		$this->mergeConfigFrom(__DIR__ . '/../config/webComponents.php', 'webComponents');
		$this->mergeConfigFrom(__DIR__ . '/../config/webBits.php', 'webBits');
		//$this->mergeConfigFrom(__DIR__ . '/../config/webCollections.php', 'webCollections');
		$this->mergeConfigFrom(__DIR__ . '/../config/webPage.php', 'webPage');
		
		$this->app->bind(ComponentAdminPageCollector::class);
		$this->app->tag(ComponentAdminPageCollector::class, 'admin-pages');
		//$this->mergeConfigFrom(__DIR__ . '/../config/webPage.php', 'webPage');
	}
	
	public function boot()
	{
		$this->loadViewsFrom(__DIR__ . '/../resources/views', 'webpage');
		
		//register the livewire components for the admin panel
		//Livewire::component('web-component-admin', WebComponentAdmin::class);
		Livewire::component('bit-text', TextBitAdmin::class);
		Livewire::component('bit-image', ImageBitAdmin::class);
		Livewire::component('sub-component', SubComponentAdmin::class);
		Livewire::component('generic-component-admin', GenericComponentAdmin::class);
		Livewire::component('gallery-component-admin', ImageGalleryComponentAdmin::class);
		
		//load the migrations and register the commands
		if ($this->app->runningInConsole()) {
			$this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
			$this->commands([
				CreateRecipeCommand::class,
				RunRecipeCommand::class,
			]);
		}
	}
}