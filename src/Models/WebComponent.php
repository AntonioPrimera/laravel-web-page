<?php

namespace AntonioPrimera\WebPage\Models;

use AntonioPrimera\WebPage\Traits\CleansUp;
use AntonioPrimera\WebPage\Traits\HasBits;
use AntonioPrimera\WebPage\Traits\HasComponents;
use AntonioPrimera\WebPage\Traits\HasParent;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @property int $id
 *
 * @property string $type
 * @property string $name
 * @property string $uid
 * @property int    $parent_id
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class WebComponent extends WebItem implements HasMedia
{
	use SoftDeletes, InteractsWithMedia, HasComponents, HasParent, HasBits, CleansUp;
	
	protected $guarded = [];
	protected $table = 'lwp_components';
	
	//--- Abstract method implementations -----------------------------------------------------------------------------
	
	public function itemPath(): string
	{
		return $this->parent instanceof WebItem
			? $this->parent->itemPath() . '.' . $this->uid
			: $this->uid;
	}
}