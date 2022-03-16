<div>
	@if(!$bits->isEmpty())
		<div class="space-y-6">
			@foreach($bits as $bit)
				@livewire($bit->getAdminView(), $bit->getAdminViewData(), key($bit->id))
			@endforeach
		</div>
	@endif

	@if(!$components->isEmpty())
		<div class="space-y-6">
			@foreach($components as $component)
				@livewire($component->getAdminView(), $component->getAdminViewData(), key($component->id))
			@endforeach
		</div>
	@endif
</div>