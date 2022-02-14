<?php

namespace AntonioPrimera\WebPage\Recipes;

use AntonioPrimera\WebPage\Facades\BitDictionary;
use AntonioPrimera\WebPage\Facades\ComponentDictionary;
use AntonioPrimera\WebPage\Managers\ComponentManager;

class Recipe
{
	
	public function __construct()
	{
		ComponentDictionary::loadDefinitions($this->defineComponents());
		ComponentDictionary::loadAliases($this->defineComponentAliases());
		
		BitDictionary::loadAliases($this->defineBitAliases());
	}
	
	//--- Hooks - to be overridden if necessary -----------------------------------------------------------------------
	
	/**
	 * Override the recipe method and return the component recipe to be created
	 * by the WebPage ComponentManager. The recipe should contain a list of
	 * 'components' and can define components and bits recursively
	 */
	public function recipe(): array
	{
		return [
			//'Page:HomePage' => [
			//	'components' => [
			//		'Section:Header' => [...],
			//		...
			//	],
			//	'bits' => [
			//		'Title:PageTitle', ...
			//	],
			//],
		];
	}
	
	public function defineComponents(): array
	{
		return [
			//define your components: 'componentType' => ['components' => [...], 'bits' => [...]]
		];
	}
	
	public function defineComponentAliases(): array
	{
		return [
			//define your component aliases: 'componentType' => 'aliasedComponentType'
		];
	}
	
	public function defineBitAliases(): array
	{
		return [
			//define your bit aliases: 'bitType' => 'aliasedBitType'
		];
	}
	
	//--- Recipe management -------------------------------------------------------------------------------------------
	
	/**
	 * Override this method if you want to take full control
	 * over the creation of components and bits
	 */
	public function up()
	{
		$this->cookRecipe($this->recipe(), webPage()->componentManager());
	}
	
	/**
	 * Override this method if you want to take full control
	 * over the deletion of components and bits
	 */
	public function down()
	{
		$this->trashRecipe($this->recipe(), webPage()->componentManager());
	}
	
	//--- Protected helpers -------------------------------------------------------------------------------------------
	
	/**
	 * A basic recipe cooker. Takes a recipe (a list of components)
	 * and creates all components and bits recursively.
	 */
	protected function cookRecipe(array $recipe, ComponentManager $manager)
	{
		//it is optional to add a root components item (only components can be defined at WebPage level)
		$components = $recipe['components'] ?? $recipe;
		
		foreach ($components as $description => $componentRecipe) {
			$manager->createComponent(
				is_numeric($description) ? $componentRecipe : $description,
				is_numeric($description) ? [] : $componentRecipe
			);
		}
	}
	
	/**
	 * A basic recipe trasher. Takes a recipe and deletes all components
	 * and bits recursively. This only soft-deletes everything.
	 */
	protected function trashRecipe(array $recipe, ComponentManager $manager)
	{
		//it is optional to add a root components item (only components can be defined at WebPage level)
		$components = $recipe['components'] ?? $recipe;
		
		foreach ($components as $description => $componentRecipe) {
			$componentDescription = is_numeric($description) ? $componentRecipe : $description;
			$uid = $manager->decomposeItemDescription($componentDescription)['uid'];
			$manager->delete($uid);
		}
	}
}