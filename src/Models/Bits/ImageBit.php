<?php

namespace AntonioPrimera\WebPage\Models\Bits;

use AntonioPrimera\WebPage\Http\Livewire\BitAdmin\ImageBitAdmin;
use AntonioPrimera\WebPage\Models\WebBit;
use AntonioPrimera\WebPage\Traits\WithBitMedia;
use Spatie\MediaLibrary\HasMedia;

class ImageBit extends WebBit implements HasMedia
{
	use WithBitMedia;
	
	public function toHtml()
	{
		$media = $this->getMediaInstance(webPage()->getLanguage(), true);
		if (!$media)
			return '';
		
		$src = $media->getUrl();
		$alt = $media->getCustomProperty('alt', '-');
		
		return "<img src='$src' alt='$alt'>";
	}
	
	public function getAdminViewComponent(): string
	{
		return ImageBitAdmin::class;
	}
}