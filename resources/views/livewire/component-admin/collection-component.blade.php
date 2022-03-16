<div class="relative">
	@if(!$items->isEmpty())
		<div class="space-y-6">
			@foreach($items as $item)
				@livewire($item->getAdminView(), $item->getAdminViewData(), key($item->id))
			@endforeach
		</div>
	@endif

	<div wire:click="createItem" class="absolute top-0 right-0 mt-8 mr-8 rounded-full bg-green-500 border shadow flex justify-center items-center p-8">
		<svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
			<path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
		</svg>
	</div>

</div>