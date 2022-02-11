<?php

namespace AntonioPrimera\WebPage\Console\Commands;

use AntonioPrimera\WebPage\Exceptions\RecipeException;
use AntonioPrimera\WebPage\Facades\BitDictionary;
use AntonioPrimera\WebPage\Facades\ComponentDictionary;
use AntonioPrimera\WebPage\Migrations\Recipe;
use Illuminate\Console\Command;

class RunRecipeCommand extends Command
{
	protected $signature = "web-page:recipe {name : the base class name of the recipe, from folder app/WebPage/Recipes}
											{--down : whether to tear down the recipe, by running its down() method}";
	
	protected $description = "This method runs the public up() method of the given WebPage Recipe. All recipes should
							  be found in folder app/WebPage/Recipes, with namespace App\\WebPage\\Recipes.
							  To run the down() method, add the --down flag to the command call";
	
	public function handle()
	{
		try {
			$recipeClass = 'App\\WebPage\\Recipes\\' . $this->argument('name');
			$recipe = $this->createRecipeInstance($recipeClass);
			$this->loadComponentsFromRecipe($recipe);
			$this->runRecipe($recipe);
			
			$message = "Recipe $recipeClass "
				. ($this->option('down') ? "cleanup has been run successfully." : "has been run successfully.");
			
			$this->info($message);
			
			return 0;
			
		} catch (RecipeException $exception) {
			$this->error($exception->getMessage());
			return 1;
		}
	}
	
	/**
	 * @throws RecipeException
	 */
	protected function createRecipeInstance($recipeClass): Recipe
	{
		if (!class_exists($recipeClass)) {
			throw new RecipeException(
				"Could not find recipe $recipeClass. Please make sure your recipe exists."
			);
		}
		
		if (!is_subclass_of($recipeClass, Recipe::class)) {
			throw new RecipeException(
				"Recipe $recipeClass must extend class " . Recipe::class
			);
		}
		
		return new $recipeClass;
	}
	
	protected function loadComponentsFromRecipe(Recipe $recipe)
	{
		ComponentDictionary::loadDefinitions($recipe->defineComponents());
		ComponentDictionary::loadAliases($recipe->defineComponentAliases());
		
		BitDictionary::loadAliases($recipe->defineBitAliases());
	}
	
	protected function runRecipe(Recipe $recipe)
	{
		$method = $this->option('down') ? 'down' : 'up';
		$recipe->$method(webPage()->componentManager());
	}
}