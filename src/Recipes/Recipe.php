<?php

namespace AntonioPrimera\WebPage\Recipes;

use AntonioPrimera\WebPage\Models\WebComponent;
use AntonioPrimera\WebPage\WebPage;

class Recipe
{
	protected array $componentDefinitions;
	protected array $bitDefinitions;
	protected array $webComponentsConfig;
	protected array $webBitsConfig;
	
	public function __construct()
	{
		$this->webComponentsConfig = config('webComponents', []);
		$this->webBitsConfig = config('webBits', []);
		$this->componentDefinitions = array_merge($this->defineComponents(), config('webComponents', []));
		$this->bitDefinitions = array_merge($this->defineBits(), config('webBits', []));
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
	
	//--- Recipe management -------------------------------------------------------------------------------------------
	
	/**
	 * Override this method if you want to take full control
	 * over the creation of components and bits
	 */
	public function up()
	{
		$this->cookRecipe($this->recipe(), webPage());
	}
	
	/**
	 * Override this method if you want to take full control
	 * over the deletion of components and bits
	 */
	public function down()
	{
		$this->trashRecipe($this->recipe(), webPage());
	}
	
	//--- Protected helpers -------------------------------------------------------------------------------------------
	
	/**
	 * A basic recipe cooker. Takes a recipe (a list of components)
	 * and creates all components and bits recursively.
	 */
	protected function cookRecipe(array $recipe, WebPage | WebComponent $owner)
	{
		//it is optional to add a root components item (only components can be defined at WebPage level)
		$components = $recipe['components'] ?? $recipe;
		
		try {
			$this->mergeDefinitionsIntoConfig();
			
			foreach ($components as $description => $componentRecipe) {
				$owner->createComponent(
					is_numeric($description) ? $componentRecipe : $description,
					is_numeric($description) ? null : $componentRecipe
				);
			}
		} finally {
			$this->resetOriginalConfig();
		}
	}
	
	/**
	 * A basic recipe trasher. Takes a recipe and deletes all components
	 * and bits recursively. This only soft-deletes everything.
	 */
	protected function trashRecipe(array $recipe, WebPage | WebComponent $owner)
	{
		//it is optional to add a root components item (only components can be defined at WebPage level)
		$components = $recipe['components'] ?? $recipe;
		$bits = $recipe['bits'] ?? [];
		
		foreach ($components as $description => $componentRecipe) {
			$componentDescription = is_numeric($description) ? $componentRecipe : $description;
			$uid = decomposeWebItemDescription($componentDescription)['uid'];
			$owner->remove($uid);
		}
		
		foreach ($bits as $description => $bitRecipe) {
			$bitDescription = is_numeric($description) ? $bitRecipe : $description;
			$uid = decomposeWebItemDescription($bitDescription)['uid'];
			$owner->remove($uid);
		}
	}
	
	public function defineComponents(): array
	{
		return [];
	}
	
	public function defineBits(): array
	{
		return [];
	}
	
	//--- Protected helpers -------------------------------------------------------------------------------------------
	
	protected function mergeDefinitionsIntoConfig()
	{
		config(['webComponents' => $this->componentDefinitions]);
		config(['webBits' => $this->bitDefinitions]);
	}
	
	protected function resetOriginalConfig()
	{
		config(['webComponents' => $this->webComponentsConfig]);
		config(['webBits' => $this->webBitsConfig]);
	}
	
	//protected function getComponentDefinition($type)
	//{
	//	if (!($this->componentDefinitions[$type] ?? null))
	//		return null;
	//
	//	//string definitions are considered to be aliases, so a recursive call is made to resolve the alias
	//	return is_string($this->componentDefinitions[$type])
	//		? $this->getComponentDefinition($this->componentDefinitions['type'])
	//		: $this->componentDefinitions['type'];
	//}
	//
	//protected function getBitDefinition($type)
	//{
	//	if (!($this->bitDefinitions[$type] ?? null))
	//		return null;
	//
	//	//string definitions are considered to be aliases, so a recursive call is made to resolve the alias
	//	return is_string($this->bitDefinitions[$type])
	//		? $this->getBitDefinition($this->bitDefinitions['type'])
	//		: $this->bitDefinitions['type'];
	//}
}