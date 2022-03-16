@props(['language'])

{{-- All Attributes are added to the actual input --}}
<div class="mt-1 flex rounded-md shadow-sm">
	<span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm"> {{ strtoupper($language) }} </span>
	<input {{ $attributes->except(['class']) }} class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300">
</div>
