<?php

/** @noinspection PhpIllegalPsrClassPathInspection */
namespace App\WebPage\Recipes;

use AntonioPrimera\WebPage\Managers\ComponentManager;
use AntonioPrimera\WebPage\Migrations\Recipe;

class SampleRecipe extends Recipe
{
	protected array $recipe = [
		'Pagina:HomePage' => [
			'components' => [
				'Sectiune:Header' => [
					'components' => [
						'Titlu', 'Imagine:Logo', 'Imagine:Hero'
					],
				],
				'Cta:Hai La Noi',
				'Cta:Inscrie-te'
			],
			
			'bits' => [
				'Titlu:Titlu Pagina'
			],
		],
	];
	
	//public function up(ComponentManager $manager)
	//{
	//	parent::up($manager);
	//}
	
	public function defineComponents(): array
	{
		return [
			'Pagina' => [],
			'Sectiune' => [],
			'Buton' => [
				'bits' => ['Eticheta', 'Url:Link'],
			],
			'Imagine' => [
				'bits' => [
					'Url', 'Alt'
				],
			],
			
			'Cta' => [
				'components' => [
					'Buton:Actiune'
				],
				
				'bits' => [
					'Titlu', 'Descriere'
				]
			],
		];
	}
	
	public function defineComponentAliases(): array
	{
		return [];
	}
	
	public function defineBitAliases(): array
	{
		return [
			'Titlu' => 'TextScurt',
			'Eticheta' => 'TextScurt',
			'Text' => 'TextLung',
			'Descriere' => 'TextLung',
		];
	}
}