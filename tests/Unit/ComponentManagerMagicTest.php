<?php
namespace AntonioPrimera\WebPage\Tests\Unit;

use AntonioPrimera\WebPage\Tests\TestCase;
use AntonioPrimera\WebPage\Tests\TestContext\ComponentManagerTester;
use AntonioPrimera\WebPage\Tests\Traits\CustomAssertions;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\ExpectationFailedException;

class ComponentManagerMagicTest extends TestCase
{
	use CustomAssertions;
	
	protected ComponentManagerTester $cm;
	
	protected function setUp(): void
	{
		parent::setUp();
		
		$this->cm = new ComponentManagerTester();
	}
	
	/** @test */
	public function it_can_decompose_a_predefined_component_creation_method_name()
	{
		$testCases = [
			'createHomePagePage' 		=> ['type' => 'Page', 'name' => 'HomePage'],
			'createHomePage' 			=> ['type' => 'Page', 'name' => 'Home'],
			'createCtaSection' 			=> ['type' => 'Section', 'name' => 'Cta'],
			'createPicturesCollection'	=> ['type' => 'Collection', 'name' => 'Pictures'],
		];
		
		foreach ($testCases as $methodName => $expectedOutput) {
			$this->assertDecomposition($methodName, $expectedOutput);
		}
		
		//and a negative case, to make sure there is no flaw in the test engine
		$this->expectException(ExpectationFailedException::class);
		$this->assertDecomposition('createHomepage', ['type' => 'Page', 'name' => 'Home']);
	}
	
	/** @test */
	public function it_can_decompose_a_bit_creation_method_name()
	{
		$testCases = [
			'createHomepage' 	=> ['type' => 'Bit', 'name' => 'Homepage', 'definition' => $this->cm->defaultBitDefinition()],
			'createHomeText' 	=> ['type' => 'Bit', 'name' => 'HomeText', 'definition' => ['type' => 'Text', 'fields' => config('webComponents.bits.Text')]],
			'createCtaLink' 	=> ['type' => 'Bit', 'name' => 'CtaLink', 'definition' => ['type' => 'Link', 'fields' => config('webComponents.bits.Link')]],
			'createHeaderImage'	=> ['type' => 'Bit', 'name' => 'HeaderImage', 'definition' => ['type' => 'Image', 'fields' => config('webComponents.bits.Image')]],
			'createTitle'		=> ['type' => 'Bit', 'name' => 'Title', 'definition' => ['type' => 'Title', 'fields' => config('webComponents.bits.Title')]],
			'createSubTitle'	=> ['type' => 'Bit', 'name' => 'SubTitle', 'definition' => ['type' => 'Title', 'fields' => config('webComponents.bits.Title')]],
			'createDescription'	=> ['type' => 'Bit', 'name' => 'Description', 'definition' => ['type' => 'Description', 'fields' => config('webComponents.bits.Description')]],
		];
		
		foreach ($testCases as $methodName => $expectedOutput) {
			$this->assertDecomposition($methodName, $expectedOutput);
		}
		
		//and a negative case, to make sure there is no flaw in the test engine
		$this->expectException(ExpectationFailedException::class);
		$this->assertDecomposition('createHomepage', ['type' => 'Page', 'name' => 'Home']);
	}
	
	//--- Internal test: Testing the protected helpers ----------------------------------------------------------------
	
	/** @test */
	public function internal_test_assert_arrays_are_same_works_as_expected()
	{
		$this->assertArraysAreSame([], []);
		$this->assertArraysAreSame(['type' => 't'], ['type' => 't']);
		
		$this->assertArraysAreSame(
			['t' => 'a', 'm' => ['x' => 'y', 'z' => 'w']],
			['m' => ['z' => 'w', 'x' => 'y'], 't' => 'a']
		);
		
		$this->expectException(ExpectationFailedException::class);
		$this->assertArraysAreSame(
			['t' => 'a', 'm' => ['x' => 'y', 'z' => 'w']],
			['m' => ['z' => 'w', 'x' => 'u'], 't' => 'a']
		);
	}
	
	//--- Protected helpers -------------------------------------------------------------------------------------------
	
	protected function assertDecomposition($methodName, $expectedRecipe)
	{
		$actualRecipe = $this->cm->_decomposeMagicCreationMethod($methodName);
		$this->assertArraysAreSame($actualRecipe, $expectedRecipe);
	}
}