<?php

namespace AntonioPrimera\WebPage;

class WebPageServiceProvider extends \Illuminate\Support\ServiceProvider
{
	public function register()
	{
		$this->app->singleton(WebPage::class, function($app) {
			return new WebPage();
		});
		
		$this->mergeConfigFrom(__DIR__ . '/../config/webComponents.php', 'webComponents');
	}
	
	public function boot()
	{
		//load the migrations
		if ($this->app->runningInConsole()) {
			$this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
		}
	}
}