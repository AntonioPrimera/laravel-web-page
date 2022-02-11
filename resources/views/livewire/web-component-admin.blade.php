<div>
	<div class="bg-white overflow-hidden shadow rounded-md divide-y divide-gray-200 mb-6">
		<div class="px-4 py-2 sm:px-6 font-medium bg-blue-100">
			{{ $component->name }}
		</div>
		<div class="px-4 py-5 sm:p-6">
			@foreach($component->bits as $bit)
				@livewire($bit->definition->getEditor(), ['bit' => $bit])
			@endforeach

			@foreach($component->components as $component)
				@livewire('web-component-admin', ['component' => $component])
			@endforeach
		</div>
	</div>


	{{--	This is a web component admin view for component {{ $componentInstance->name }}--}}
</div>