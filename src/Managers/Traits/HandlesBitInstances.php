<?php

namespace AntonioPrimera\WebPage\Managers\Traits;

use AntonioPrimera\WebPage\Models\Bit;
use AntonioPrimera\WebPage\Models\Component;
use Illuminate\Support\Str;

trait HandlesBitInstances
{
	use ManagerHelpers;
	
	protected array $bits = [];
	
	protected function cacheBit(?Bit $bit)
	{
		if (!$bit)
			return null;
		
		$this->bits[$bit->uid] = $bit;
		return $bit;
	}
	
	//--- Bit creation ------------------------------------------------------------------------------------------------
	
	protected function createBit(string $description): ?Bit
	{
		if (!$this->owner instanceof Component)
			return null;
		
		//decompose the item description
		[$type, $name, $uid] = $this->itemDescription($description);
		
		$validUid = $uid ?: Str::kebab($name);
		
		//check if we don't already have the bit
		if ($existingBit = $this->getBit($validUid))
			return $existingBit;
		
		//only create the bit if the given type is defined
		return $this->bitTypeIsDefined($type)
			? $this->createNewBit($type, $name, $validUid)
			: null;
	}
	
	protected function createNewBit(string $type, string $name, string $uid)
	{
		/** @noinspection PhpParamsInspection */
		return $this->cacheBit(
			$this->owner->bits()->create(compact('type', 'name', 'uid'))
		);
	}
	
	//--- Bit retrieval -----------------------------------------------------------------------------------------------
	
	public function getBit(string $uid)
	{
		return $this->getCachedBit($uid) ?: $this->getBitFromOwnerComponent($uid);
	}
	
	protected function getCachedBit(string $uid)
	{
		return $this->bits[$uid] ?? null;
	}
	
	protected function getBitFromOwnerComponent(string $uid)
	{
		if (!$this->owner instanceof Component)
			return null;
		
		return $this->cacheBit($this->owner->bits()->whereUid($uid)->first());
	}
}