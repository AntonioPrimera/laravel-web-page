<?php

namespace AntonioPrimera\WebPage\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string 				$language
 * @property WebComponent | null 	$parent
 * @property int | null 			$parent_id
 *
 * @property string 				$type
 * @property string					$name
 * @property string 				$uid
 */
class WebItem extends Model
{
	const IS_LEAF = false;
	
	public function parent()
	{
		return $this->belongsTo(WebComponent::class, 'parent_id', 'id');
	}
	
	//--- Uid Path Management -----------------------------------------------------------------------------------------
	
	public function itemPath(): string
	{
		$separator = static::IS_LEAF ? ':' : '.';
		
		$parentPath = $this->parent instanceof WebItem
			? $this->parent->itemPath() . $separator
			: '';
		
		return $parentPath . $this->uid;
	}
}