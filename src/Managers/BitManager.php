<?php

namespace AntonioPrimera\WebPage\Managers;

use AntonioPrimera\WebPage\Models\Component;

class BitManager
{
	protected array $bits = [];
	
	/**
	 * The owner of this BitManager instance
	 * (who's bits are we managing)
	 */
	protected Component $owner;
	
	public function __construct(Component $owner)
	{
		$this->owner = $owner;
	}
	
	public function __call(string $name, array $arguments)
	{
		//todo: implement this
	}
	
	//--- Getters and setters -----------------------------------------------------------------------------------------
	
	public function getOwner(): Component
	{
		return $this->owner;
	}
	
	//--- Magic helpers -----------------------------------------------------------------------------------------------
}