<?php

namespace AntonioPrimera\WebPage\Http\Livewire\ComponentAdmin;

use AntonioPrimera\WebPage\Http\Livewire\WebComponentAdmin;
use Illuminate\Support\Collection;

class GenericComponentAdmin extends WebComponentAdmin
{
	public Collection $components;
	public Collection $bits;
	
	public function mount($component)
	{
		$this->webComponent = $this->getComponentInstance($component);
		$this->components = $this->webComponent->getComponents();
		$this->bits = $this->webComponent->getBits();
	}
	
	public function render()
	{
		return view('webpage::livewire.component-admin.generic-component-admin');
	}
}