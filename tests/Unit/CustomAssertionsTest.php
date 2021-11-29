<?php
namespace AntonioPrimera\WebPage\Tests\Unit;

use AntonioPrimera\WebPage\Tests\TestCase;
use AntonioPrimera\WebPage\Tests\TestContext\ComponentManagerTester;
use AntonioPrimera\WebPage\Tests\Traits\CustomAssertions;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\ExpectationFailedException;

class CustomAssertionsTest extends TestCase
{
	use CustomAssertions;
	
	/** @test */
	public function internal_test_assert_arrays_are_same_works_as_expected()
	{
		$this->assertArraysAreSame([], []);
		$this->assertArraysAreSame(['type' => 't'], ['type' => 't']);
		
		$this->assertArraysAreSame(
			['t' => 'a', 'm' => ['x' => 'y', 'z' => 'w'], 's'],
			['m' => ['z' => 'w', 'x' => 'y'], 's', 't' => 'a']
		);
		
		$this->expectException(ExpectationFailedException::class);
		$this->assertArraysAreSame(
			['t' => 'a', 'm' => ['x' => 'y', 'z' => 'w']],
			['m' => ['z' => 'w', 'x' => 'u'], 't' => 'a']
		);
	}
}