@props(['w-model', 'label', 'value'])

<div class="flex" x-data="{showInput: false, showTextarea: false}" wire:key="{{ $wModel }}">
	<div class="py-2 px-3 font-bold">{{ $label }}</div>

	<div class="flex-1 flex items-center">
		{{-- Display property value --}}
		<div x-show="!showInput && !showTextarea"
			 @click="showInput = $refs.input.value.length < 30; showTextarea = !showInput; $nextTick(() => showInput ? $refs.input.focus() : $refs.textarea.focus());"
			 class="max-w-full w-full px-3 py-2 break-words"
		>
			{!! lineBrakesToParagraphs($value) !!}
		</div>

		{{-- Edit property value (either input or textarea) --}}
		<input x-cloak x-show="showInput" x-ref="input" class="w-full min-w-0 block px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" type="text" wire:model="{{ $wModel }}" @blur="showInput = false">
		<textarea x-cloak x-show="showTextarea" x-ref="textarea" class="w-full h-auto min-w-0 block px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" wire:model="{{ $wModel }}" @blur="showTextarea = false"></textarea>
	</div>
</div>