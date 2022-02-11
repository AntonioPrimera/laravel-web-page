<?php

namespace AntonioPrimera\WebPage\Tests\Traits;

use AntonioPrimera\WebPage\Models\Bit;
use AntonioPrimera\WebPage\Models\WebComponent;
use AntonioPrimera\WebPage\Tests\TestContext\ComponentManagerTester;
use Illuminate\Support\Arr;

trait ComponentAssertions
{
	
	protected function assertComponentMissing($componentUid)
	{
		$this->assertNull(WebComponent::whereUid($componentUid)->first());
	}
	
	protected function assertComponentExists($componentUid)
	{
		$this->assertInstanceOf(WebComponent::class, WebComponent::whereUid($componentUid)->first());
	}
	
	protected function assertIsComponent($component)
	{
		$this->assertInstanceOf(WebComponent::class, $component);
		return $this;
	}
	
	protected function assertIsBit($bit)
	{
		$this->assertInstanceOf(Bit::class, $bit);
		return $this;
	}
	
	protected function assertHasComponents($component, $expectedComponentUids, $strict = true)
	{
		$this->assertIsComponent($component);
		/* @var WebComponent $component */
		
		$components = $component->components;
		$actualComponentUids = collect($components)->pluck('uid')->toArray();
		
		$diff = array_diff(Arr::wrap($expectedComponentUids), $actualComponentUids);
		$this->assertEmpty($diff, 'Following components are missing: ' . implode(', ', $diff));
		
		if ($strict)
			$this->assertCount(count(Arr::wrap($expectedComponentUids)), $actualComponentUids);
		
		return $this;
	}
	
	protected function assertHasBits($component, $expectedBitUids, $strict = true)
	{
		$this->assertIsComponent($component);
		
		$bits = $component->bits;
		$actualBitUids = collect($bits)->pluck('uid')->toArray();
		
		$this->assertEmpty(
			array_diff(Arr::wrap($expectedBitUids), $actualBitUids),
			'Missing bits: ' . implode(', ', array_diff(Arr::wrap($expectedBitUids), $actualBitUids))
		);
		
		if ($strict)
			$this->assertCount(count(Arr::wrap($expectedBitUids)), $actualBitUids);
		
		
		return $this;
	}
	
	protected function assertComponentDetails($component, $type, $name, $uid)
	{
		$this->assertIsComponent($component);
		$this->assertEquals($type, $component->type);
		$this->assertEquals($name, $component->name);
		$this->assertEquals($uid, $component->uid);
	}
	
	protected function assertBitDetails($bit, $type, $name, $uid)
	{
		$this->assertIsBit($bit);
		$this->assertEquals($type, $bit->type);
		$this->assertEquals($name, $bit->name);
		$this->assertEquals($uid, $bit->uid);
	}
}