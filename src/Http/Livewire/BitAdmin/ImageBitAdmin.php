<?php

namespace AntonioPrimera\WebPage\Http\Livewire\BitAdmin;

use AntonioPrimera\WebPage\Models\Bits\ImageBit;
use Livewire\Component;
use Livewire\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ImageBitAdmin extends Component
{
	use WithFileUploads;
	
	public ?ImageBit $bit;
	
	public array $media = [];
	
	public function mount($bit)
	{
		$this->bit = $this->getWebBit($bit);
		
		$this->setupMediaSet();
	}
	
	public function render()
	{
		return view('webpage::livewire.bit-admin.image-bit-admin');
	}
	
	protected function getWebBit($bit)
	{
		if ($bit instanceof ImageBit)
			return $bit;
		
		if (is_string($bit))
			return webPage()->get($bit);
		
		return null;
	}
	
	public function updated($key, $value)
	{
		//e.g.: $key = "media.en.properties.alt"
		$keyParts = explode('.', $key);
		
		if ($keyParts[0] === 'media' && ($keyParts[2] ?? null) === 'properties')
			$this->updateCustomProperty($keyParts[1] ?? null, $keyParts[3] ?? null, $value);
		
		if ($keyParts[0] === 'media' && ($keyParts[2] ?? null) === 'file')
			$this->uploadFile($keyParts[1] ?? null);
	}
	
	public function removeMedia($language)
	{
		$media = $this->bit->getMediaInstance($language);
		if ($media)
			$media->delete();
		
		$this->bit->refresh();
		$this->setupMediaSet();
	}
	
	//--- Protected helpers -------------------------------------------------------------------------------------------
	
	protected function updateCustomProperty($language, $property, $value)
	{
		$media = $this->bit->getMediaInstance($language);
		
		if (!$media)
			return;
		
		$media->setCustomProperty($property, $value);
		$media->save();
		
		if ($language === webPage()->getFallbackLanguage())
			$this->updateFallbackCustomProperties($property, $value);
	}
	
	protected function uploadFile($language)
	{
		$this->validate([
			"media.$language.file" => 'image',
		]);
		
		$file = $this->media[$language]['file'];
		/* @var TemporaryUploadedFile $file */
		
		$currentMedia = $this->bit->getMediaInstance($language);
		
		$this->bit
			->addMedia($file->getRealPath())
			->withCustomProperties($this->bit->getCustomProperties($currentMedia))	//copy the custom properties over
			->sanitizingFileName(function($fileName) {
				return strtolower(str_replace(['#', '/', '\\', ' ', "'", '"'], '-', $fileName));
			})
			->usingName($file->getClientOriginalName())
			->toMediaCollection($language, $this->bit->getMediaDisk());
		
		$this->bit->refresh();
		$this->setupMediaSet();
	}
	
	/**
	 * Populate the $this->media array, representing all
	 * available media items for the current bit
	 */
	protected function setupMediaSet()
	{
		foreach (webPage()->getLanguages() as $language => $details)
			$this->setupMediaSetItem($language, $this->bit->getMediaInstance($language, true));
	}
	
	protected function setupMediaSetItem(string $language, ?Media $media)
	{
		$this->media[$language] = [
			'exists' 	 => $this->bit->hasMedia($language),
			'url' 		 => $media ? $media->getUrl() : null,
			'properties' => $this->bit->getCustomProperties($media),
			'file'		 => null,
		];
	}
	
	protected function updateFallbackCustomProperties($property, $value)
	{
		$fallbackProperties = $this->media[webPage()->getFallbackLanguage()]['properties'];
		
		foreach ($this->media as $language => $mediaSet) {
			if (!$mediaSet['exists'])
				$this->media[$language]['properties'] = $fallbackProperties;
		}
	}
}