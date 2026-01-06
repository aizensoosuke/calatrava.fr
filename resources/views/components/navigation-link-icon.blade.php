@props([
    'icon' => 'cube',
    'label' => null,
    'url' => '#',
    'class' => ''
])

<a href="{{ $url }}" class="{{ $class }}">
    <div class="flex flex-col items-center group font-semibold">
        <div class="mb-1">
            <x-icon :name="$icon" />
        </div>
        @if($label !== null)
            <div class="group-hover:underline ">
                {{ $label }}
            </div>
        @endif
    </div>
</a>
