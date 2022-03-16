<?php

namespace AntonioPrimera\WebPage\Http\Livewire\ComponentAdmin;

use AntonioPrimera\WebPage\Http\Livewire\WebComponentAdmin;
use Illuminate\Support\Collection;

class CollectionAdmin extends WebComponentAdmin
{
	public Collection $items;
	
	public function mount($component)
	{
		$this->webComponent = $this->getComponentInstance($component);
		$this->items = $this->webComponent->getComponents();
	}
	
	public function createItem()
	{
		$this->webComponent->createItem();
	}
	
	public function render()
	{
		return view('webpage::livewire.component-admin.collection-component');
	}
}