<?php

namespace AntonioPrimera\WebPage\Models\Components;

use AntonioPrimera\WebPage\Http\Livewire\ComponentAdmin\ImageGalleryComponentAdmin;
use AntonioPrimera\WebPage\Models\WebComponent;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ImageGalleryComponent extends WebComponent implements HasMedia
{
	use InteractsWithMedia;
	
	/**
	 * Override this list to define
	 * other media conversions
	 */
	protected array $mediaSizes = [
		'small'   => 375,
		'medium'  => 750,
		'large'   => 1280,
		//'full-hd' => 1920,
		//'4k'	  => 3840,
	];
	
	/**
	 * The list of custom media properties, each media item can have.
	 * Override this with your own custom properties.
	 */
	protected array $mediaProperties = ['alt', 'label'];
	
	/**
	 * The accepted mime types
	 */
	protected array $mimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/svg', 'image/webp'];
	
	protected bool $responsiveImages = false;
	
	//--- MediaLibrary setup ------------------------------------------------------------------------------------------
	
	public function registerMediaConversions(Media $media = null): void
	{
		//generate media conversions for all defined sizes
		foreach ($this->mediaSizes as $name => $width) {
			$this->addMediaConversion($name)
				->width($width);
		}
	}
	
	public function registerMediaCollections(): void
	{
		//$collection = $this->getMediaCollection();
		//
		//$collection->acceptsMimeTypes($this->mimeTypes);
		//
		//if ($this->responsiveImages)
		//	$collection->withResponsiveImages();
	}
	
	//--- Public methods ----------------------------------------------------------------------------------------------
	
	public function getCustomProperties(Media $media)
	{
		return collect($this->mediaProperties)
			->mapWithKeys(function($propertyName) use ($media) {
				return [$propertyName => $media->getCustomProperty($propertyName)];
			})
			->toArray();
	}
	
	public function getAdminViewComponent(): string
	{
		return ImageGalleryComponentAdmin::class;
	}
	
	public function getMediaDisk()
	{
		$itemPath = $this->itemPath();
		
		return config("webPage.disks.$itemPath")
			?: config("webPage.disks.$this->uid")
			?: config('webPage.disks.default')
			?: 'public';
	}
}