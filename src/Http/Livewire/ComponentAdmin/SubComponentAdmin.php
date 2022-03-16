<?php

namespace AntonioPrimera\WebPage\Http\Livewire\ComponentAdmin;

use AntonioPrimera\WebPage\Http\Livewire\WebComponentAdmin;

/**
 * Displays a card with a sub-component. This is used to
 */
class SubComponentAdmin extends WebComponentAdmin
{
	
	public function mount($component)
	{
		$this->webComponent = $this->getComponentInstance($component);
	}
	
	public function render()
	{
		return view('webpage::livewire.component-admin.sub-component');
	}
}