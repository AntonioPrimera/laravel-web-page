<?php

namespace AntonioPrimera\WebPage\Traits;

use Illuminate\Support\Facades\DB;

trait RetrievesComponents
{
	protected function retrieveComponent($id)
	{
		$rawComponent = $this->componentsTable()->find($id);
		return $this->createWebComponentFromRawData($rawComponent);
	}
	
	protected function createWebComponentFromRawData($rawAttributes)
	{
		$rawAttributeArray = (array) $rawAttributes;
		return (new $rawAttributeArray['class_name'])->newFromBuilder($rawAttributeArray);
	}
	
	protected function componentsTable()
	{
		return DB::table('lwp_components');
	}
}