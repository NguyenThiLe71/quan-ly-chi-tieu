@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-pink-600 dark:text-orange-300']) }}>
    {{ $value ?? $slot }}
</label>