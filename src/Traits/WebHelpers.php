<?php

namespace AntonioPrimera\WebPage\Traits;

use AntonioPrimera\WebPage\Models\Bit;
use AntonioPrimera\WebPage\Models\WebComponent;

trait WebHelpers
{
	
	/**
	 * Get the component or attribute at a given dot separated uid path, optionally
	 * followed by a dot separated attribute path. The component path is
	 * separated from the attribute path by a colon ":"
	 *
	 * Format: dot.separated.component.uid.path:bit-uid
	 * (the bit-uid is optional)
	 *
	 * e.g. $componentManager->get('homepage.header.image')
	 * 		will get the image component under the header component, under the homepage component
	 *
	 * e.g. $componentManager->get('homepage.header.image:url')
	 * 		will get the url Bit instance from the image component
	 */
	public function get(string $path): WebComponent | Bit | null
	{
		//split the path into a component path and an attribute path
		$pathParts = explode(':', $path, 2);
		$uidPath = $pathParts[0];
		$bitUid = $pathParts[1] ?? null;
		
		//search for the component
		$component = $this->getComponent($uidPath);
		
		return $component && $bitUid
			? $component->getBit($bitUid)
			: $component;
	}
}