<?php

namespace AntonioPrimera\WebPage\Traits;

use Illuminate\Support\Collection;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait WebItemMedia
{
	use InteractsWithMedia;
	
	/**
	 * The list of custom media properties, each media item can have.
	 * Override this with your own custom properties.
	 */
	protected array $mediaProperties = ['alt', 'label'];
	
	public function getCustomProperties(?Media $media): array
	{
		$itemPath = $this->itemPath();
		
		$mediaProperties = config("webPage.mediaProperties.$itemPath")		//priority 1: full item path
			?: config("webPage.mediaProperties.$this->uid")				//priority 2: uid
			?: config('webPage.' . static::class . '.mediaProperties')		//priority 3: class specific config
			?: config('webPage.mediaProperties.default')					//priority 4: configured default set
			?: $this->mediaProperties;
			
		return Collection::wrap($mediaProperties)
			->mapWithKeys(function($propertyName) use ($media) {
				return [$propertyName => $media ? $media->getCustomProperty($propertyName) : null];
			})
			->toArray();
	}
	
	public function registerMediaConversions(Media $media = null): void
	{
		foreach ($this->getMediaConversions() as $name => $definition) {
			//we don't create empty conversions
			if (!$definition)
				continue;
			
			$conversion = $this->addMediaConversion($name);
			
			//--- Size manipulation ------------------------------------
			
			if (is_int($definition['width'] ?? null))
				$conversion->width($definition['width']);
			
			if (is_int($definition['height'] ?? null))
				$conversion->height($definition['height']);
			
			if (
				is_array($definition['fit'] ?? null)
				&& is_int($definition['fit']['width'] ?? null)
				&& is_int($definition['fit']['height'] ?? null)
			)
				$conversion->fit(
					$definition['fit']['method'] ?? Manipulations::FIT_CONTAIN,
					$definition['fit']['width'],
					$definition['fit']['height']
				);
			
			//--- Format and quality -------------------------------------
			
			if (is_string($definition['format'] ?? null))
				$conversion->format($definition['format']);
			
			if (is_int($definition['quality'] ?? null))
				$conversion->quality($definition['quality']);
			
			if ($definition['optimize'] ?? false)
				$conversion->optimize(is_array($definition['optimize']) ? $definition['optimize'] : []);
			
			//--- Artsy Effects -------------------------------------------
			
			if (is_int($definition['blur'] ?? null))
				$conversion->blur($definition['blur']);
			
			if (is_int($definition['sharpen'] ?? null))
				$conversion->sharpen($definition['sharpen']);
			
			if (is_int($definition['pixelate'] ?? null))
				$conversion->pixelate($definition['pixelate']);
			
			if (is_int($definition['brightness'] ?? null))
				$conversion->brightness($definition['brightness']);
			
			if ($definition['sepia'] ?? false)
				$conversion->sepia();
			
			if ($definition['greyscale'] ?? false)
				$conversion->greyscale();
		}
	}
	
	public function getMediaConversions()
	{
		return config('webPage.' . static::class . '.mediaConversions')
			?: config("webPage.mediaConversions")
				?: $this->getDefaultMediaConversions();
	}
	
	public function getMediaDisk()
	{
		$itemPath = $this->itemPath();
		
		return config("webPage.disks.$itemPath")				//priority 1: full item path
			?: config("webPage.disks.$this->uid")				//priority 2: uid
				?: config('webPage.' . static::class . '.disk')	//priority 3: class specific config
					?: config('webPage.disks.default')					//priority 4: configured default disk
						?: 'public';											//priority 5: default disk
	}
	
	//--- Protected methods -------------------------------------------------------------------------------------------
	
	protected function getDefaultMediaConversions()
	{
		return [
			//'small' => [
			//	'width'  => 375,
			//],
			//'medium' => [
			//	'width' => 750,
			//],
			//'large'	 => [
			//	'width' => 1280,
			//],
		];
	}
}