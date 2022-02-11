<?php

namespace AntonioPrimera\WebPage\Console\Commands;

use AntonioPrimera\Artisan\FileGeneratorCommand;
use AntonioPrimera\Artisan\FileRecipe;

class CreateRecipeCommand extends FileGeneratorCommand
{
	protected $signature = "web-page:make:recipe {name : the class name of the recipe to be created} {--dry-run}";
	
	protected function recipe(): array
	{
		$recipe = new FileRecipe(__DIR__ . '/../stubs/RecipeStub.php.stub', 'WebPage/Recipes');
		$recipe->rootNamespace = 'App\\WebPage\\Recipes';
		
		return [
			'Web Page Recipe' => $recipe,
		];
	}
}