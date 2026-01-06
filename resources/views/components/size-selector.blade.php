@props([
    'product' => null,
    'xSelectedColorId' => 'selectedColorId'
])

@php
use App\Data\ProductData;
/** @var ProductData $product */
@endphp

<div class="py-4 absolute bottom-0 w-full bg-white/60 flex flex-col items-center">
    <div class='flex justify-around gap-8'>
        @foreach($product->variants as $variant)
            <button
                x-on:click="$wire.addToCart('{{ $variant->id }}')"
                x-show="{{ $xSelectedColorId }} == '{{ $variant->colorId }}'"
                x-cloak
                class="
                    py-1 px-2 text-sm
                    @if($variant->stock > 0)
                        opacity-100 hover:bg-white active:opacity-70
                        focus:outline focus:outline-1 focus:outline-offset-1
                    @else
                        line-through opacity-60 cursor-default
                    @endif
                    "
                title="{{ $variant->stock > 0 ? 'Ajouter au panier' : 'Indisponible' }}"
            >
                {{ $variant->size }}
            </button>
        @endforeach
    </div>
</div>
