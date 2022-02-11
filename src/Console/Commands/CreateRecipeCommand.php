<?php

namespace AntonioPrimera\WebPage\Console\Commands;

use AntonioPrimera\Artisan\FileGeneratorCommand;
use AntonioPrimera\Artisan\FileRecipe;

class CreateRecipeCommand extends FileGeneratorCommand
{
	protected $signature = "web-page:make:recipe {name : the class name of the recipe to be created}
												 {--dry-run : do a dry run, do not create any files}
												 {--complex : create a complex recipe}";
	
	protected function recipe(): array
	{
		$stubFile = $this->hasOption('complex')
			? __DIR__ . '/../stubs/ComplexRecipeStub.php.stub'
			: __DIR__ . '/../stubs/RecipeStub.php.stub';
		
		$recipe = new FileRecipe($stubFile, 'WebPage/Recipes');
		$recipe->rootNamespace = 'App\\WebPage\\Recipes';
		
		return [
			'Web Page Recipe' => $recipe,
		];
	}
}