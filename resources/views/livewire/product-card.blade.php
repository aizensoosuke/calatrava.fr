<?php

use App\Data\ProductData;
use App\Models\Product;
use Lunar\Facades\CartSession;
use Lunar\Models\Cart;
use Lunar\Models\Channel;
use Lunar\Models\Currency;
use Lunar\Models\ProductVariant;
use Illuminate\Validation\ValidationException;

use function \Livewire\Volt\state;
use function \Livewire\Volt\mount;
use function \Livewire\Volt\action;
use function \Livewire\Volt\on;
use function \Livewire\Volt\protect;

/** @var ProductData $product */
state([
    'product' => null,
])->locked();

mount(function (ProductData $product) {
    $this->product = $product;
});

on([
    'cart-updated' => fn () => $this->refresh()
]);

$refresh = protect(function () {
    $product = Product::find($this->product->id);
    $this->product = ProductData::fromProductModel($product);
});

$addToCart = action(function ($id) {
    $purchasable = ProductVariant::find($id);

    if ($purchasable === null) {
        $this->status = 'Product not found.';
        $this->dispatch('cart-updated');
        return;
    }

    if ($purchasable->stock <= 0) {
        $this->status = 'Product out of stock.';
        $this->dispatch('cart-updated');
        return;
    }

    if ($purchasable->product?->status != 'published') {
        return;
    }

    $purchasable->decrement('stock');

    $cart = CartSession::current();

    if (is_null($cart)) {
        $cart = Cart::create([
            'currency_id' => Currency::firstWhere('default', true)->id,
            'channel_id' => Channel::firstWhere('default', true)->id,
        ]);
        CartSession::use($cart);
        if (auth()->check()) {
            $cart->associate(auth()->user());
        }
    }

    $line = $cart->lines
        ->where('purchasable_type', $purchasable->getMorphClass())
        ->where('purchasable_id', $purchasable->id)
        ->first();

    // TODO: handle validation errors
    if ($line !== null) {
        $line->increment('quantity');
        $this->status = 'Product added to cart.';
        $this->dispatch('open-cart-popover');
        $this->dispatch('cart-updated');
        return;
    }

    $cart->lines()->create([
        'purchasable_type' => $purchasable->getMorphClass(),
        'purchasable_id' => $purchasable->id,
        'quantity' => 1,
    ]);

    $this->status = 'Product added to cart.';

    $this->dispatch('cart-updated');
    $this->dispatch('open-cart-popover');
});

?>

<div
    class="mb-6"
    x-data="{
        'selectedColorId': $wire.product.defaultColorId,
        'open': false
    }"
>
    <div class="relative overflow-hidden group" x-on:click="open = !open">
        @if(! $product->isAvailable)
            <div class='absolute top-0 z-20 text-sm text-white bg-gray-600 text-center py-2 px-3 ml-4 mt-4'>
                Victime de son succès
            </div>
        @endif
        <x-product-carousel :$product />
        <div :class="{'hidden group-hover:block': !open}" x-cloak>
            <x-size-selector :$product x-selected-color-id="selectedColorId" />
        </div>
    </div>
    <div class='flex flex-row px-1 pt-3 justify-between'>
        <div>
            @if(request()->routeIs('product'))
            <div>{{ $product->name }}</div>
            @else
            <a class="hover:underline" href="{{ route('product', $product->slug) }}">{{ $product->name }}</a>
            @endif
            <div>{{ $product->price }} €</div>
        </div>
    </div>
    <div class='flex gap-2 mx-1 mt-2'>
        @foreach($product->colors as $color)
            <button
                x-on:click="selectedColorId = '{{ $color->id }}'"
                :class="{'bg-gray-200': selectedColorId == '{{ $color->id }}'}"
                class="
                    py-1 px-2 text-sm
                    opacity-100 bg-gray-100 hover:bg-gray-200 active:opacity-70
                    focus:outline focus:outline-1 focus:outline-offset-1
                    "
            >
                {{ $color->name }}
            </button>
        @endforeach
    </div>
</div>
