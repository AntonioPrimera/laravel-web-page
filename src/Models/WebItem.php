<?php

namespace AntonioPrimera\WebPage\Models;

use AntonioPrimera\WebPage\WebPage;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int | null 			$parent_id
 *
 * @property string 				$type
 * @property string					$name
 * @property string 				$uid
 */
abstract class WebItem extends Model
{
	//--- Custom Relations --------------------------------------------------------------------------------------------
	public abstract function getParent(): WebComponent | WebPage | null;
	
	//--- Uid Path Management -----------------------------------------------------------------------------------------
	public abstract function itemPath(): string;
}