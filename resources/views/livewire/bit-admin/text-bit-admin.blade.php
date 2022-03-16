<div>
	<span class="mb-1 text-sm font-bold text-gray-700">{{ $webBit->name }}</span>

	<div class="space-y-4">
		@foreach(webPage()->getLanguages() as $language => $details)
			@if($type === 'textbox')
				<x-webpage::language-textbox language="{{ $language }}" wire:model="data.{{ $language }}"/>
			@else
				<x-webpage::language-input language="{{ $language }}" wire:model="data.{{ $language }}"/>
			@endif
		@endforeach
	</div>
</div>