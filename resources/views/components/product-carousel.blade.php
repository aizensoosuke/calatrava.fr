@props([
    'product' => null
])

@php
use App\Data\ProductData;

/** @var ProductData $product */
@endphp

<div
    class="
        @if(! $product->isAvailable) opacity-60 @endif
        "
>
    <div class="glide relative">
        <div class="glide__track" data-glide-el="track">
            <ul class="glide__slides">
                @foreach($product->carousel as $image)
                    <li class="glide__slide">
                        <img src="{{ $image->url }}" alt="{{ $image->alt }}" />
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="glide__arrows absolute w-full h-full flex flex-row justify-between items-center top-0" data-glide-el="controls">
            <button
                class="glide__arrow glide__arrow--left h-full cursor-pointer p-4 select-none text-xl"
                data-glide-dir="<"
            >
                ❮
            </button>
            <button class="glide__arrow glide__arrow--right h-full cursor-pointer p-4 select-non text-xl" data-glide-dir=">">
                ❯
            </button>
        </div>
    </div>

</div>
