{{-- Card --}}
<div class="bg-white overflow-hidden shadow rounded-lg divide-y divide-gray-200">
	{{-- Card Header --}}
	<div class="px-4 py-5 sm:px-6 bg-blue-100">
		<span class="font-bold">{{ $webComponent->name }}</span> <span class="text-gray-500">({{ $webComponent->type }})</span>
	</div>

	{{-- Card Contents --}}
	<div class="px-4 py-5 sm:p-6 space-y-8">
		@livewire('generic-component-admin', ['component' => $webComponent])
	</div>
</div>