@props(['type' => 'text', 'name', 'value' => '', 'label' => ''])

<div class="mb-4">
    @if($label)
        <label class="block font-bold">{{ $label }}</label>
    @endif
    <input type="{{ $type }}" name="{{ $name }}" value="{{ old($name, $value) }}"
           {{ $attributes->merge(['class' => 'input input-bordered w-full']) }}>
    @error($name) <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
</div>