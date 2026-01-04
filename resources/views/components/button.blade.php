@props([
    'url' => null,
    'class' => '',
    'primary' => null,
    'danger' => null,
    'disabled' => false,
    'xOnclick' => '',
])


<button
    @if($disabled)
        disabled
    @endif

    x-on:click="{{ $xOnclick }}"

    class="
        @if($primary !== null)
            bg-gray-800 hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 text-white
            focus:ring-indigo-500
        @elseif($danger !== null)
            bg-red-500 hover:bg-gray-600 focus:bg-gray-600 active:bg-gray-800 text-white
            focus:ring-red-500
        @else
            bg-white hover:bg-gray-50 text-gray-700
            focus:ring-indigo-500
        @endif
        cursor-pointer
        relative inline-flex items-center
        border border-gray-300
        px-4 py-2
        text-xs font-semibold uppercase tracking-widest
        shadow-xs
        transition duration-150 ease-in-out
        focus:outline-none focus:ring-2 focus:ring-offset-2
        disabled:opacity-25
        {{ $class }}
        "
>
    @if(! is_null($url))
        <a href="{{ $url }}" class="absolute w-full h-full"></a>
    @endif

    {{ $slot }}
</button>
