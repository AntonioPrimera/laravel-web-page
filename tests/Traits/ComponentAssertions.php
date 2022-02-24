<?php

namespace AntonioPrimera\WebPage\Tests\Traits;

use AntonioPrimera\WebPage\Models\Bit;
use AntonioPrimera\WebPage\Models\WebComponent;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

trait ComponentAssertions
{
	protected function assertComponentMissing($componentUid)
	{
		$this->assertDatabaseMissing($this->componentsTable, ['uid' => $componentUid, 'deleted_at' => null]);
	}
	
	protected function assertComponentIsSoftDeleted($componentUid)
	{
		$this->assertNotNull(
			DB::table($this->componentsTable)
				->where('uid', $componentUid)
				->whereNotNull('deleted_at')
				->first()
		);
	}
	
	protected function assertComponentExists($componentUid)
	{
		$this->assertDatabaseHas($this->componentsTable, ['uid' => $componentUid]);
	}
	
	protected function assertIsComponent($component)
	{
		$this->assertInstanceOf(WebComponent::class, $component);
		$this->assertInstanceOf($component->class_name, $component);
		return $this;
	}
	
	protected function assertIsBit($bit)
	{
		$this->assertInstanceOf(Bit::class, $bit);
		$this->assertInstanceOf($bit->class_name, $bit);
		return $this;
	}
	
	protected function assertHasComponents(WebComponent $component, $expectedComponentUids, $strict = true)
	{
		$actualComponentUids = (array) DB::table($this->componentsTable)
			->select('uid')
			->where('parent_id', $component->id)
			->get()
			->pluck('uid')
			->values()
			->toArray();
		
		$diff = array_diff(Arr::wrap($expectedComponentUids), $actualComponentUids);
		$this->assertEmpty($diff, 'Missing components: ' . implode(', ', $diff));
		
		if ($strict)
			$this->assertCount(count(Arr::wrap($expectedComponentUids)), $actualComponentUids);
		
		return $this;
	}
	
	protected function assertHasBits(WebComponent $component, $expectedBitUids, $strict = true)
	{
		$actualBitUids = DB::table($this->bitsTable)
			->select('uid')
			->where('parent_id', $component->id)
			->get()
			->pluck('uid')
			->values()
			->toArray();
		
		$diff = array_diff(Arr::wrap($expectedBitUids), $actualBitUids);
		$this->assertEmpty(
			array_diff(Arr::wrap($expectedBitUids), $actualBitUids),
			'Missing bits: ' . implode(', ', $diff)
		);
		
		if ($strict)
			$this->assertCount(count(Arr::wrap($expectedBitUids)), $actualBitUids);
		
		return $this;
	}
	
	protected function assertComponentDetails(WebComponent $component, $type, $name, $uid)
	{
		$this->assertEquals($type, $component->type);
		$this->assertEquals($name, $component->name);
		$this->assertEquals($uid, $component->uid);
	}
	
	protected function assertBitDetails(Bit $bit, $type, $name, $uid)
	{
		$this->assertEquals($type, $bit->type);
		$this->assertEquals($name, $bit->name);
		$this->assertEquals($uid, $bit->uid);
	}
}