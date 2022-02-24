<?php

namespace AntonioPrimera\WebPage\Traits;

use AntonioPrimera\WebPage\Models\Bit;
use AntonioPrimera\WebPage\Models\WebComponent;
use Illuminate\Support\Collection;

trait HasBits
{
	use RetrievesBits;
	
	protected Collection | null $bits = null;
	
	//--- Bit creation ------------------------------------------------------------------------------------------------
	
	public function createBit(string $description): ?Bit
	{
		//decompose the item description
		['type' => $type, 'name' => $name, 'uid' => $uid] = decomposeWebItemDescription($description);
		
		//check if we don't already have the bit
		if ($existingBit = $this->getBit($uid))
			return $existingBit;
		
		//if only pre-defined bits should be created and this type is not defined, exit
		$definition = $this->getBitDefinition($type);
		return $this->cacheBit($this->createOwnBit($type, $name, $uid, $definition['class'] ?? null));
		
		//if ($onlyDefined && !BitDictionary::isDefined($type))
		//	return null;
		//
		//$bit = $this->owner->bits()->create(compact('type', 'name', 'uid'));
		//
		////reset any loaded bits
		//$this->resetBits();
		//
		///* @var Bit $bit */
		//return $bit;
	}
	
	//--- Bit retrieval -----------------------------------------------------------------------------------------------
	
	public function getBits($readFresh = false)
	{
		if (!$this->bits || $readFresh)
			$this->bits = $this->retrieveBits($this instanceof WebComponent ? $this->id : null);
		
		return $this->bits;
	}
	
	public function getBit(string $uid): ?Bit
	{
		if ($this->bits && $this->bits->has($uid))
			return $this->bits->get($uid);
		
		return $this->cacheBit($this->retrieveOwnBit($uid));
	}
	
	public function resetBits()
	{
		$this->bits = null;
	}
	
	//--- Protected helpers -------------------------------------------------------------------------------------------
	
	protected function createOwnBit(string $type, string $name, string $uid, ?string $class)
	{
		$bitClass = is_subclass_of($class, Bit::class)
			? $class
			: Bit::class;
		
		//create a new bit of the given component class
		$bit = $bitClass::create([
			'parent_id'  => $this instanceof WebComponent ? $this->id : null,
			'class_name' => $bitClass,
			'type' 		 => $type,
			'name' 		 => $name,
			'uid'  		 => $uid,
		]);
		
		//invalidate the loaded component list (will be loaded fresh when needed)
		$this->cacheBit($bit);
		
		return $bit;
	}
	
	protected function cacheBit(?Bit $bit): Bit | null
	{
		if (!$bit)
			return null;
		
		if (!$this->bits)
			$this->bits = collect();
		
		$this->bits->put($bit->uid, $bit);
		
		return $bit;
	}
	
	protected function retrieveBits(int | string | null $parentId): Collection
	{
		$rawBits = $this->bitsTable()
			->where('parent_id', $parentId)
			->get();
		
		return $rawBits
			->map(fn($rawAttributes) => $this->cacheBit($this->createBitFromRawData($rawAttributes)));
	}
	
	//--- Component definitions ---------------------------------------------------------------------------------------
	
	protected function getBitDefinition($type)
	{
		$definition = config("webBits.$type");
		if (!$definition)
			return [];
		
		//string definitions are considered to be aliases, so a recursive call is made to resolve the alias
		return is_string($definition)
			? $this->getBitDefinition($definition)
			: $definition;
	}
}