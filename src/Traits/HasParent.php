<?php

namespace AntonioPrimera\WebPage\Traits;

use AntonioPrimera\WebPage\Models\WebComponent;
use AntonioPrimera\WebPage\WebPage;

trait HasParent
{
	protected WebComponent | WebPage | null $parent = null;
	
	public function setParent(WebComponent | WebPage $parent)
	{
		$this->parent = $parent;
		
		if ($parent instanceof WebComponent && $this->parent_id != $parent->id)
			$this->update(['parent_id' => $parent->id]);
		
		if ($parent instanceof WebPage && $this->parent_id)
			$this->update(['parent_id' => null]);
	}
	
	public function getParent(): WebComponent|WebPage|null
	{
		if ($this->parent_id === null)
			return webPage();
		
		return $this->retrieveComponent($this->parent_id);
	}
}