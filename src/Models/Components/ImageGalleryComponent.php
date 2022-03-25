<?php

namespace AntonioPrimera\WebPage\Models\Components;

use AntonioPrimera\WebPage\Http\Livewire\ComponentAdmin\ImageGalleryComponentAdmin;
use AntonioPrimera\WebPage\Models\WebComponent;
use AntonioPrimera\WebPage\Traits\WebItemMedia;
use Spatie\MediaLibrary\HasMedia;

class ImageGalleryComponent extends WebComponent implements HasMedia
{
	use WebItemMedia;
	
	/**
	 * The accepted mime types
	 */
	protected array $mimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/svg', 'image/webp'];
	
	protected bool $responsiveImages = false;
	
	//--- MediaLibrary setup ------------------------------------------------------------------------------------------
	
	public function getAdminViewComponent(): string
	{
		return ImageGalleryComponentAdmin::class;
	}
}