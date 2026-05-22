@props(['disabled' => false])

<input
    @disabled($disabled)
    {{ $attributes->merge([
        'class' => 'border-pink-300 focus:border-orange-400 focus:ring-orange-400 rounded-md shadow-sm'
    ]) }}
>