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
    'status' => ''
])->locked();

mount(fn($product) => $this->product = $product);

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
    }

    if ($purchasable->stock <= 0) {
        $this->status = 'Product out of stock.';
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
        return;
    }

    $cart->lines()->create([
        'purchasable_type' => $purchasable->getMorphClass(),
        'purchasable_id' => $purchasable->id,
        'quantity' => 1,
    ]);

    $this->status = 'Product added to cart.';

    $this->dispatch('cart-updated');
});

?>

<div class="mb-6">
    <div class="relative overflow-hidden group">
        @if(! $product->isAvailable)
            <div class='absolute top-0 rounded-sm z-20 text-sm text-white bg-gray-700 text-center py-2 px-3 ml-4 mt-4'>
                Rupture
            </div>
        @endif
        <x-product-carousel :$product />
        <div class="hidden group-hover:block">
            <x-size-selector :$product />
        </div>
    </div>
    <div class='flex flex-row px-1 py-3 justify-between'>
        <div>
            <div>{{ $product->name }}</div>
            <div>{{ $product->price }} €</div>
        </div>
        <div class='cursor-pointer'>
            <x-icon name="heart" />
        </div>
    </div>
</div>
