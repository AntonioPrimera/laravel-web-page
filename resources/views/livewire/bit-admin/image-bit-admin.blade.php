<div>
	<span class="mb-1 text-sm font-bold text-gray-700">{{ $bit->name }}</span>

	<div class="grid grid-cols-3 gap-6">
		@foreach($media as $language => $mediaData)
			<div class="border border-gray-300 rounded-md divide-y divide-gray-300">

				{{-- Media Card Header --}}
				<div class="py-2.5 px-3 bg-gray-50 text-gray-500 sm:text-sm rounded-t-md flex justify-between">
					<span>{{ strtoupper($language) }}</span>

					@if($mediaData['exists'])
						<svg wire:click="removeMedia('{{$language}}')" xmlns="http://www.w3.org/2000/svg" class="w-4 text-gray-700 hover:text-red-500 cursor-pointer" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
							<path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
						</svg>
					@else
						<span> </span>
					@endif
				</div>

				{{-- Media Image Preview --}}
				<x-webpage::upload language="{{ $language }}" wire:model="media.{{ $language }}.file">
					<x-webpage::square-container class="">
						@if($mediaData['url'])
							<x-webpage::image-preview src="{{ $mediaData['url'] }}"/>
						@else
							<div class="flex w-full h-full items-center justify-center text-gray-300">
								<svg xmlns="http://www.w3.org/2000/svg" class="w-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
									<path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
								</svg>
							</div>
						@endif
					</x-webpage::square-container>
				</x-webpage::upload>

				{{-- Media Details (Custom Properties) --}}
				@foreach($mediaData['properties'] as $name => $value)
					<div class="flex" x-data="{mediaExists: {{ $mediaData['exists'] ? 'true' : 'false' }}, showInput: false, showTextarea: false}" wire:key="{{ $language . '-' . $name . '-' . ((int) $mediaData['exists']) }}">
						<div class="py-2 px-3 font-bold">{{ $name }}</div>

						<div class="flex-1 flex items-center">
							{{-- Display property value --}}
							<div x-show="!showInput && !showTextarea" @click="if (mediaExists) {showInput = $refs.input.value.length < 30; showTextarea = !showInput; $nextTick(() => showInput ? $refs.input.focus() : $refs.textarea.focus());}" class="max-w-full w-full px-3 py-2 break-words">{{ $value }}</div>

							{{-- Edit property value (either input or textarea) --}}
							<input x-cloak x-show="mediaExists && showInput" x-ref="input" class="w-full min-w-0 block px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" type="text" wire:model="media.{{$language}}.properties.{{ $name }}" @blur="showInput = false">
							<textarea x-cloak x-show="mediaExists && showTextarea" x-ref="textarea" class="w-full h-auto min-w-0 block px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" wire:model="media.{{$language}}.properties.{{ $name }}" @blur="showTextarea = false"></textarea>
						</div>
					</div>
				@endforeach
			</div>
		@endforeach
	</div>
</div>