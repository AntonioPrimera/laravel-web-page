<?php

namespace AntonioPrimera\WebPage\Http\Livewire\BitEditors;

class Input extends \Livewire\Component
{
	public string $type = 'text';
	
	public function render()
	{
		return view('web-page::livewire.bit-editors.input');
	}
	
	
}