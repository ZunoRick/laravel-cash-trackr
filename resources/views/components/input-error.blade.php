@props(['field'])

@error($field)
    <p class="my-2 px-2 border border-red-400 text-red-700 bg-red-100 py-3 text-sm">{{ $message }}</p>
@enderror
