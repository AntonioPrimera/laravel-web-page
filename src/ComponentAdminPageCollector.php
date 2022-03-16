<?php

namespace AntonioPrimera\WebPage;

use AntonioPrimera\WebPage\Models\Components\ImageGalleryComponent;
use AntonioPrimera\WebPage\Models\WebComponent;

class ComponentAdminPageCollector
{
	
	public function resolve()
	{
		$components = \AntonioPrimera\WebPage\Facades\WebPage::getComponents();
		
		return $components->mapWithKeys(fn(WebComponent $component) => [
			$this->adminPageUid($component) => [
				'name' 		=> $component->name,
				'uid'		=> $this->adminPageUid($component),
				'icon'		=> $this->adminPageIcon($component),
				//'menuLabel' => $component->name,
				//'position' 	=> 0,	//todo: add positioning
				//'url'		=> $this->adminPageUid($component),
				'view'		=> $component->getAdminViewComponent(),
				'viewData'	=> $component->getAdminViewData(),
			]
		])->toArray();
	}
	
	protected function adminPageUid(WebComponent $component): string
	{
		return 'component-admin-' . $component->uid;
	}
	
	protected function adminPageIcon(WebComponent $component): string
	{
		if ($component instanceof ImageGalleryComponent)
			return 'heroicon:photograph';
		
		if ($component->type === 'Page')
			return 'heroicon:template';
		
		//todo: add smart determination of icon, based on $component->type and component class
		
		return 'heroicon:cube';
	}
}