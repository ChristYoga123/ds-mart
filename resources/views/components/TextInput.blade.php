@props(['type' => 'text', 'name', 'placeholder', 'label', 'required' => true])
<div>
    <label for="{{ $name }}"
        class="block text-sm font-medium text-gray-700 @error($name) text-red-500 @enderror">{{ $label ?? ucwords(str_replace('_', ' ', $name)) }}</label>
    <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" {{ $required ? 'required' : '' }}
        placeholder="{{ $placeholder }}"
        class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-100 px-3 py-2 focus:border-gray-700 focus:ring-gray-700 text-gray-900" />
    @error($name)
        <p class="text-red-500 text-sm">{{ $message }}</p>
    @enderror
</div>
