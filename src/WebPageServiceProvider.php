<?php

namespace AntonioPrimera\WebPage;

use AntonioPrimera\WebPage\Console\Commands\CreateRecipeCommand;
use AntonioPrimera\WebPage\Console\Commands\RunRecipeCommand;
use Illuminate\Support\ServiceProvider;

use AntonioPrimera\WebPage\Http\Livewire\WebComponentAdmin;
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
		//$this->mergeConfigFrom(__DIR__ . '/../config/webPage.php', 'webPage');
	}
	
	public function boot()
	{
		//$this->loadViewsFrom(__DIR__ . '/../resources/views', 'web-page');
		//
		////register the livewire components for the admin panel
		//Livewire::component('web-component-admin', WebComponentAdmin::class);
		//Livewire::component('web-bit-admin', WebComponentAdmin::class);
		
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