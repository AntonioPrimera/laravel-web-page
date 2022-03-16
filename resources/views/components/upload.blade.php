<div onclick="this.querySelector('[hidden-file-upload-input]').click()" {{ $attributes->only('class') }}>
	{{ $slot }}
	<input hidden-file-upload-input type="file" class="sr-only" {{ $attributes->except('class') }}>
</div>