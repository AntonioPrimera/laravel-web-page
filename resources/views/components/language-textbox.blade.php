@props(['language'])

{{-- All Attributes are added to the actual input --}}

<div>
	<div class="mt-1 flex rounded-md shadow-sm">
		<div class="block border-0 px-3 py-2.5 text-md bg-gray-50 font-medium focus:ring-0 rounded-l-md">{{ $language }}</div>
		<textarea rows="4"
				  class="py-2.5 px-3 focus:ring-indigo-500 focus:border-indigo-500 block flex-1 sm:text-sm border-gray-300 rounded-r-md"
				  {{ $attributes->except('class') }}
		></textarea>
	</div>
</div>
