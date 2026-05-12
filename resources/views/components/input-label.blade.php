@props(['value'])

<label {{ $attributes->merge(['class' => 'vx-label']) }}>
    {{ $value ?? $slot }}
</label>
