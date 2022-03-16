<?php

namespace AntonioPrimera\WebPage\Traits;

use AntonioPrimera\WebPage\Models\WebBit;
use AntonioPrimera\WebPage\Models\WebComponent;
use AntonioPrimera\WebPage\WebPage;
use Illuminate\Support\Facades\DB;

trait RetrievesBits
{
	
	protected function retrieveOwnBit(string $uid): WebBit | null
	{
		$rawBit = $this->bitsTable()
			->where('parent_id', $this instanceof WebComponent ? $this->id : null)
			->where('uid', $uid)
			->first();
		
		return $rawBit ? $this->createBitFromRawData($rawBit) : null;
	}
	
	protected function retrieveBit($id)
	{
		$rawBit = $this->bitsTable()->find($id);
		return $this->createBitFromRawData($rawBit);
	}
	
	protected function createBitFromRawData($rawAttributes)
	{
		$rawAttributeArray = (array) $rawAttributes;
		return (new $rawAttributeArray['class_name'])->newFromBuilder($rawAttributeArray);
	}
	
	protected function bitsTable()
	{
		return DB::table('lwp_bits');
	}
}