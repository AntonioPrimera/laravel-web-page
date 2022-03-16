<div>

	@error('imageFiles')
		<div x-data="{showError: true}" x-show="showError" class="my-4 border-red-300 bg-red-100 px-4 py-3 text-red-600 flex rounded-md">
			<p class="flex-1">
				ImageFiles error
{{--				{{ $message }}--}}
			</p>

			<svg xmlns="http://www.w3.org/2000/svg" class="w-4 cursor-pointer" fill="none" viewBox="0 0 24 24" stroke="currentColor" @click="showError = false">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
			</svg>
		</div>
	@enderror

	<div class="grid grid-cols-4 gap-6">

		{{-- The upload file placeholder --}}
		<div class="shadow">
			<x-webpage::upload wire:model="imageFiles" multiple>
				<x-webpage::square-container class="">
					<div class="flex w-full h-full items-center justify-center text-gray-300">
						<svg xmlns="http://www.w3.org/2000/svg" class="w-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
							<path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
						</svg>
					</div>
				</x-webpage::square-container>
			</x-webpage::upload>
		</div>

		{{-- The list of existing images --}}
		@foreach($gallery as $mediaId => $mediaData)

			<div class="shadow">
				<x-webpage::square-container class="bg-white relative">
					<x-webpage::image-preview src="{{ $mediaData['url'] }}" key="{{ $mediaId }}"/>

					<svg wire:click="removeImage({{ $mediaId }})" xmlns="http://www.w3.org/2000/svg" class="absolute top-0 right-0 mt-2 mr-2 h-8 w-8 hover:text-red-500 text-gray-500 cursor-pointer" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
						<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
					</svg>
				</x-webpage::square-container>

				<div class="divide-y divide-gray-300">
					@foreach($mediaData['customProperties'] as $name => $value)
						<x-webpage::smart-input w-model="gallery.{{ $mediaId }}.customProperties.{{ $name }}" label="{{ $name }}" value="{{ $value }}"/>
{{--						<div class="p-3">{{ $name }} : {{ $value }}</div>--}}
{{--						<input type="text" wire:model="gallery.{{ $mediaId }}.customProperties.{{ $name }}">--}}
					@endforeach
				</div>
			</div>

{{--			<x-gallery-manager.image :image="$image" key="{{ $image->id }}"/>--}}
		@endforeach
	</div>
</div>
