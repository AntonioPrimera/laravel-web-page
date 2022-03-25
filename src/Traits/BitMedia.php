<?php

namespace AntonioPrimera\WebPage\Traits;

use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait BitMedia
{
	use WebItemMedia;
	
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
			? $this->getFirstMedia($language)
				->getUrl($conversionName ?: array_key_last($this->getMediaConversions()) ?: '')
			: null;
	}
}