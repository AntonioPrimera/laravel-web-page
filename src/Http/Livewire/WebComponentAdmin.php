<?php

namespace AntonioPrimera\WebPage\Http\Livewire;

use AntonioPrimera\WebPage\Models\WebComponent as WebComponent;
use Livewire\Component;

class WebComponentAdmin extends Component
{
	public WebComponent | null | string $component;
	
	public function mount()
	{
		//always normalize the given component into a component instance
		$this->component = $this->getComponentInstance($this->component);
	}
	
	public function render()
	{
		return view('web-page::livewire.web-component-admin');
	}
	
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