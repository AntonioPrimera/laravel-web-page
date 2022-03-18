<?php

namespace AntonioPrimera\WebPage\Traits;

use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait WithBitMedia
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
		//generate a media collection for each language, with a single file
		foreach (webPage()->getLanguages() as $language => $details) {
			$this->addMediaCollection($language)
				->singleFile();
		}
	}
	
	//--- Public methods ----------------------------------------------------------------------------------------------
	
	/**
	 * Returns the media item for the given language. If $fallbackLanguage
	 * is true and no media exists for the given language, the
	 * media for the fallback language is retrieved.
	 */
	public function getMediaInstance(string $language, bool $fallbackLanguage = false): Media | null
	{
		if ($media = $this->getFirstMedia($language))
			return $media;
		
		return $fallbackLanguage
			? $this->getFirstMedia(webPage()->getFallbackLanguage())
			: null;
	}
	
	/**
	 * Get the image url for a language and a conversion name (corresponding to a size).
	 * If the conversion name is not given, the last one is used.
	 */
	public function getImageUrl($language, $conversionName = null)
	{
		return $this->hasMedia($language)
			? $this->getFirstMedia($language)->getUrl($conversionName ?: array_key_last($this->mediaSizes))
			: null;
	}
	
	/**
	 * Returns an array with urls and sizes for all conversions for a media item:
	 * conversionName => [url => '...', 'width' => ...]
	 */
	public function getImageUrls($language): array
	{
		$urls = [];
		
		foreach ($this->getMedia($language) as $media) {
			foreach ($this->mediaSizes as $name => $width) {
				$urls[$name] = [
					'url'   => $media->getUrl($name),
					'width' => $width,
				];
			}
		}
		
		return $urls;
	}
	
	/**
	 * Get the list of custom properties [propertyName => value, ...] of a media item.
	 * If no media is given, a list of propertyNames with null values is returned.
	 * The properties are defined by the $this->mediaProperties attribute
	 */
	public function getCustomProperties(?Media $media)
	{
		$properties = [];
		
		foreach ($this->mediaProperties as $mediaProperty) {
			$properties[$mediaProperty] = $media ? $media->getCustomProperty($mediaProperty) : null;
		}
		
		return $properties;
	}
	
	public function getMediaDisk()
	{
		$itemPath = $this->itemPath();
		
		return config("webPage.disks.$itemPath")
			?: config("webPage.disks.$this->uid")
			?: config('webPage.disks.default')
			?: 'public';
	}
	
	//public function getCustomSrcset(?Media $media)
	//{
	//	if (!$media)
	//		return '';
	//
	//	foreach ($media->getGeneratedConversions())
	//}
}