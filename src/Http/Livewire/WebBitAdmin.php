<?php

namespace AntonioPrimera\WebPage\Http\Livewire;

use AntonioPrimera\WebPage\Models\Bit;
use Livewire\Component;

class WebBitAdmin extends Component
{
	public ?Bit $bitInstance;
	
	public function mount($bit)
	{
		$this->bitInstance = $this->getBitInstance($bit);
	}
	
	public function render()
	{
		//todo: create a simple, default web bit admin (short text)
		return view('web-page::livewire.web-bit-admin');
	}
	
	//--- Protected helpers -------------------------------------------------------------------------------------------
	
	protected function getBitInstance($bit)
	{
		if ($bit instanceof Bit)
			return $bit;
		
		if (is_string($bit))
			return webPage()->get($bit);
		
		return null;
	}
}