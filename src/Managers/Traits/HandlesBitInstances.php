<?php

namespace AntonioPrimera\WebPage\Managers\Traits;

use AntonioPrimera\WebPage\Facades\BitDictionary;
use AntonioPrimera\WebPage\Models\Bit;
use AntonioPrimera\WebPage\Models\WebComponent;

trait HandlesBitInstances
{
	use ManagerHelpers;
	
	//--- Bit creation ------------------------------------------------------------------------------------------------
	
	public function createBit(string $description, bool $onlyDefined = false): ?Bit
	{
		if (!$this->owner instanceof WebComponent)
			return null;
		
		//decompose the item description
		['type' => $type, 'name' => $name, 'uid' => $uid] = $this->decomposeItemDescription($description);
		
		//check if we don't already have the bit
		if ($existingBit = $this->getBit($uid))
			return $existingBit;
		
		//if only pre-defined bits should be created and this type is not defined, exit
		if ($onlyDefined && !BitDictionary::isDefined($type))
			return null;
		
		$bit = $this->owner->bits()->create(compact('type', 'name', 'uid'));
		
		//reset any loaded bits
		$this->resetBits();
		
		/* @var Bit $bit */
		return $bit;
	}
	
	//--- Bit retrieval -----------------------------------------------------------------------------------------------
	
	public function getBit(string $uid): ?Bit
	{
		return $this->owner->bits->firstWhere('uid', $uid);
	}
	
	public function resetBits()
	{
		$this->owner->unsetRelation('bits');
	}
}