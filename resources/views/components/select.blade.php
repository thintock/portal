@props(['name', 'label' => '', 'options' => [], 'value' => null])

<div class="mb-4">
    @if($label)
        <label for="{{ $name }}" class="block font-bold mb-1">{{ $label }}</label>
    @endif

    <select name="{{ $name }}" id="{{ $name }}"
        {{ $attributes->merge(['class' => 'select select-bordered w-full']) }}>
        @foreach($options as $key => $text)
            <option value="{{ $key }}" @selected(old($name, $value) == $key)>
                {{ $text }}
            </option>
        @endforeach
    </select>

    @error($name)
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
