<?php

namespace AntonioPrimera\WebPage\Models;

use AntonioPrimera\WebPage\WebPage;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component as LivewireComponent;
use Livewire\Livewire;

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
	
	//--- Bit admin view ----------------------------------------------------------------------------------------------
	
	/**
	 * Return the livewire view component for this bit
	 */
	abstract public function getAdminViewComponent(): string;
	
	public function getAdminView()
	{
		$adminViewComponent = $this->getAdminViewComponent();
		
		return is_subclass_of($adminViewComponent, LivewireComponent::class)
			? (Livewire::getAlias($adminViewComponent) ?: $adminViewComponent::getName())
			: null;
	}
	
	/**
	 * Return the data to be provided to the livewire
	 * view component inside the blade view
	 */
	public function getAdminViewData(): array
	{
		return [];
	}
}