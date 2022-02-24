<?php

/** @noinspection PhpIllegalPsrClassPathInspection */
namespace App\WebPage\Recipes;

use AntonioPrimera\WebPage\Recipes\Recipe;

class SampleRecipe extends Recipe
{
	
	public function recipe(): array
	{
		return [
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
	}
	
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
	
	public function defineBits(): array
	{
		return [
			'Titlu' => 'TextScurt',
			'Eticheta' => 'TextScurt',
			'Text' => 'TextLung',
			'Descriere' => 'TextLung',
		];
	}
}