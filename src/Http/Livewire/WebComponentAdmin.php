<?php

namespace AntonioPrimera\WebPage\Http\Livewire;

use AntonioPrimera\WebPage\Models\WebComponent as WebComponent;
use Livewire\Component;

class WebComponentAdmin extends Component
{
	public WebComponent | null | string $webComponent;
	
	//--- Protected helpers -------------------------------------------------------------------------------------------
	
	protected function getComponentInstance($component)
	{
		if ($component instanceof WebComponent)
			return $component;
		
		if (is_string($component))
			return webPage()->getComponent($component);
		
		return null;
	}
}