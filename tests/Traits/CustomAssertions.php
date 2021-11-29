<?php

namespace AntonioPrimera\WebPage\Tests\Traits;

trait CustomAssertions
{
	protected function assertArraysAreSame($expected, $actual)
	{
		$this->assertIsArray($actual);
		$this->assertEquals(count($actual), count($expected), "Expected Recipe item count differs from the actual recipe item count");
		
		foreach ($expected as $key => $value) {
			if (is_array($value)) {
				$this->assertArraysAreSame($value, $actual[$key]);
				continue;
			}
			
			if (is_numeric($key)) {
				$this->assertArrayContains($value, $actual);
			} else {
				$this->assertEquals($value, $actual[$key]);
			}
		}
		
		return $this;
	}
	
	protected function assertArrayContains($value, $array)
	{
		$this->assertTrue(in_array($value, $array), "Failed asserting that item '{$value}' is found in array: " . json_encode($array));
		return $this;
	}
}