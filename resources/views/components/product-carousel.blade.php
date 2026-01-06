@props([
    'class' => '',
    'product' => null
])

@php
use App\Data\ProductData;

/** @var ProductData $product */
@endphp

<div
    class="
        {{ $class }}
        @if(! $product->isAvailable) opacity-60 @endif
        "
>
    <div class="glide">
        <div class="glide__track" data-glide-el="track">
            <ul class="glide__slides" style="touch-action: auto">
                @foreach($product->carousel as $image)
                    <li class="glide__slide">
                        <img src="{{ $image->url }}" alt="{{ $image->alt }}" />
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="glide__arrows" data-glide-el="controls">
            <button
                x-on:dblclick.prevent=""
                class="glide__arrow glide__arrow--left absolute top-0 h-full cursor-pointer p-4 select-none text-xl"
                data-glide-dir="<"
            >
                ❮
            </button>
            <button
                x-on:dblclick.prevent=""
                class="glide__arrow glide__arrow--right absolute top-0 right-0 h-full cursor-pointer p-4 select-non text-xl"
                data-glide-dir=">"
            >
                ❯
            </button>
        </div>
    </div>

</div>
