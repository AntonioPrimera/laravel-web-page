<?php

namespace AntonioPrimera\WebPage\Http\Livewire\BitAdmin;

use AntonioPrimera\WebPage\Http\Livewire\WebBitAdmin;
use Livewire\Component;

class TextBitAdmin extends WebBitAdmin
{
	const TYPE_INPUT 	= 'input';
	const TYPE_TEXTBOX 	= 'textbox';
	
	public array $data = [];
	public string $type = self::TYPE_INPUT;
	
	public function mount($bit)
	{
		$this->webBit = $this->getWebBit($bit);
		
		foreach (webPage()->getLanguages() as $language => $details) {
			$this->data[$language] = $this->webBit->get($language);
		}
	}
	
	public function render()
	{
		return view('webpage::livewire.bit-admin.text-bit-admin');
	}
	
	public function updated($key, $value)
	{
		$language = explode('.', $key, 2)[1];
		$this->webBit->set($language, $value);
	}
}