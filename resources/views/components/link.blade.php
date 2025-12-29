@props([
    'class' => '',
    'xClick' => ''
])

<span
    x-on:click="{{ $xClick }}"
    class="
        text-sm text-gray-600 underline hover:text-gray-900
        cursor-pointer
        focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2
        {{ $class }}
        "
>
    {{ $slot }}
</span>
