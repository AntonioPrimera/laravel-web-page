<?php

namespace AntonioPrimera\WebPage\Tests\Feature;

use AntonioPrimera\Testing\CustomAssertions;
use AntonioPrimera\WebPage\Facades\WebPage;
use AntonioPrimera\WebPage\Recipes\Recipe;
use AntonioPrimera\WebPage\Models\Bit;
use AntonioPrimera\WebPage\Models\WebComponent;
use AntonioPrimera\WebPage\Tests\TestCase;
use AntonioPrimera\WebPage\Tests\Traits\ComponentAssertions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

class RunRecipeCommandTest extends TestCase
{
	use RefreshDatabase, CustomAssertions, ComponentAssertions;
	
	protected function setUp(): void
	{
		parent::setUp(); // TODO: Change the autogenerated stub
		
		//prepare the bit definitions (these should go in the config in a real scenario)
		config([
			'webBits' => [
				'TextScurt' => [
					'rules' => ['string', 'max:120'],
					'editor' => 'livewire.short-text',
				],
				'TextLung' => [
					'rules' => ['string', 'min:10'],
					'editor' => 'livewire.long-text',
				],
				'Url' => [
					'rules' => ['url'],
					'editor' => 'livewire.url',
				],
			],
		]);
		
		//load the recipe file
		require_once __DIR__ . '/../TestContext/SampleRecipe.php';
	}
	
	/** @test */
	public function it_can_run_a_recipe_via_artisan_command()
	{
		$this->assertTrue(is_subclass_of('App\\WebPage\\Recipes\\SampleRecipe', Recipe::class));
		
		$this->assertComponentMissing('home-page');
		
		Artisan::call('web-page:recipe SampleRecipe');
		
		//check root component creation
		$this->assertComponentExists('home-page');
		$component = WebPage::getComponent('home-page');
		$this->assertHasComponents($component, ['header', 'hai-la-noi', 'inscrie-te']);
		$this->assertHasBits($component, ['titlu-pagina']);
		
		//check CTA creation
		$this->assertHasComponents(WebPage::getComponent('home-page.hai-la-noi'), ['actiune']);
		$this->assertHasBits(WebPage::getComponent('home-page.hai-la-noi'), ['titlu', 'descriere']);
	}
	
	/** @test */
	public function it_can_destroy_a_recipe_using_the_down_flag()
	{
		$initialComponentCount = WebComponent::count();
		$initialBitCount = Bit::count();
		
		Artisan::call('web-page:recipe SampleRecipe');
		
		$this->assertTrue(WebComponent::count() > $initialComponentCount);
		$this->assertTrue(Bit::count() > $initialBitCount);
		$this->assertComponentExists('home-page');
		
		Artisan::call('web-page:recipe SampleRecipe --down');
		
		$this->assertEquals($initialComponentCount, WebComponent::count());
		$this->assertEquals($initialBitCount, Bit::count());
		$this->assertComponentIsSoftDeleted('home-page');
	}
}