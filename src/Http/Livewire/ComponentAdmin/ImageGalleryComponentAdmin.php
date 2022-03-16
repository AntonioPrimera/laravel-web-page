<?php

namespace AntonioPrimera\WebPage\Http\Livewire\ComponentAdmin;

use AntonioPrimera\WebPage\Http\Livewire\WebComponentAdmin;
use AntonioPrimera\WebPage\Models\Components\ImageGalleryComponent;
use AntonioPrimera\WebPage\Models\WebComponent;
use Livewire\WithFileUploads;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Displays a card with a sub-component. This is used to
 */
class ImageGalleryComponentAdmin extends WebComponentAdmin
{
	use WithFileUploads;
	
	public $gallery;
	public $imageFiles = [];
	//public $galleryImages;
	
	public function mount($component)
	{
		$this->webComponent = $this->getComponentInstance($component);
		$this->refreshGallery();
	}
	
	public function render()
	{
		return view('webpage::livewire.component-admin.image-gallery-component-admin');
	}
	
	//--- Actions -----------------------------------------------------------------------------------------------------
	
	public function updated($key, $value)
	{
		if ($key === 'imageFiles') {
			return $this->uploadFiles();
		}
		
		if (str_starts_with($key, 'gallery'))
			return $this->updateCustomProperty($key, $value);
		
		return true;
	}
	
	public function removeImage($id)
	{
		Media::find($id)->delete();
		$this->refreshGallery();
	}
	
	
	//--- Protected helpers -------------------------------------------------------------------------------------------
	
	protected function uploadFiles()
	{
		$this->validate([
			'imageFiles.*' => 'image',
		]);
		
		foreach ($this->imageFiles as $temporaryImage) {
			$this->webComponent
				->addMedia($temporaryImage->getRealPath())
				->sanitizingFileName(function($fileName) {
					return strtolower(str_replace(['#', '/', '\\', ' ', "'", '"'], '-', $fileName));
				})
				//->withResponsiveImages()
				//->usingName($temporaryImage->getClientOriginalName())
				->toMediaCollection();	//name, disk
		}
		
		$this->imageFiles = [];
		$this->refreshGallery();
		
		return true;
	}
	
	protected function updateCustomProperty($key, $value)
	{
		$parts = explode('.', $key);
		if (!(count($parts) === 4 && $parts[0] === 'gallery' && $parts[2] === 'customProperties' && is_numeric($parts[1])))
			return false;
		
		$media = Media::find($parts[1]);
		/* @var Media $media */
		if (!$media)
			return false;
		
		$media->setCustomProperty($parts[3], $value);
		$media->save();
		
		return true;
	}
	
	protected function refreshGallery()
	{
		$this->gallery = $this->webComponent
			->fresh()
			->getMedia()
			->sortByDesc('id')
			->mapWithKeys(fn(Media $media) => [
				$media->id => [
					//'media' => $media,
					'url' => $media->getUrl(),
					'customProperties' => $this->webComponent->getCustomProperties($media),
				]
			]);
	}
}