<?php

namespace AntonioPrimera\WebPage\Migrations;

use AntonioPrimera\WebPage\Managers\ComponentManager;

class Recipe
{
	/**
	 * Override the $recipe attribute or the recipe()
	 * method to return a recipe array of
	 * components to be created
	 */
	protected array $recipe = [];
	
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
	
	/**
	 * Override this method if you want to take full control
	 * over the creation of components and bits
	 */
	public function up(ComponentManager $manager)
	{
		$this->cookRecipe($this->recipe() ?: $this->recipe, $manager);
	}
	
	/**
	 * Override this method if you want to take full control
	 * over the deletion of components and bits
	 */
	public function down(ComponentManager $manager)
	{
		$this->trashRecipe($this->recipe() ?: $this->recipe, $manager);
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
			$manager->createComponent($description, $componentRecipe);
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
			$uid = $manager->decomposeItemDescription($description)['uid'];
			$manager->delete($uid);
		}
	}
}