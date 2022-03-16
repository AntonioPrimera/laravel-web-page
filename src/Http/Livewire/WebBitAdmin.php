<?php

namespace AntonioPrimera\WebPage\Http\Livewire;

use AntonioPrimera\WebPage\Models\WebBit;
use Livewire\Component;

class WebBitAdmin extends Component
{
	public ?WebBit $webBit;
	
	//--- Protected helpers -------------------------------------------------------------------------------------------
	
	protected function getWebBit($bit)
	{
		if ($bit instanceof WebBit)
			return $bit;
		
		if (is_string($bit))
			return webPage()->get($bit);
		
		return null;
	}
}