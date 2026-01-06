<?php

use App\Actions\CheckoutActions;
use App\Data\CartData;
use Lunar\Facades\CartSession;
use Lunar\Models\CartLine;
use Lunar\Models\TaxZone;

use function Livewire\Volt\protect;
use function Livewire\Volt\state;
use function Livewire\Volt\mount;
use function Livewire\Volt\action;
use function Livewire\Volt\on;

state([
    'cart' => null
]);

/** @var $cart CartData */
mount(function () {
    $this->refresh();
});

on([
    'cart-updated' => fn() => $this->refresh()
]);

$refresh = protect(function () {
    $cart = CartSession::current();
    if ($cart) {
        $this->cart = CartData::from($cart);
    }
});

$delete = action(function ($lineId) {
    $line = CartSession::current()->lines()->find($lineId);

    if ($line === null) {
        throw new Exception('Cart line not found.');
    }

    $line->decrement('quantity');
    if ($line->quantity <= 0) {
        $line->delete();
    }

    $line->purchasable()->increment('stock');

    $this->dispatch('cart-updated');
});


?>
<div class="flex flex-col p-4 w-screen -mr-8 sm:mr-0 sm:w-96 border-gray-800 bg-white border">
    @if($cart === null || $cart->lines->count() == 0)
        <div class="text-center">Votre panier est vide</div>
    @else
        <div class="h5 font-semibold uppercase">
            Panier ({{ $cart->lines->count() }})
        </div>
        <div class="flex flex-col gap-2 mt-6">
            @foreach($cart->lines as $line)
                <div wire:key="{{ $line->variant->id }}">
                    <x-cart-line :$line/>
                </div>
            @endforeach
        </div>
        <div class="mt-6 text-sm">
            <div class="flex flex-col">
                <div class="flex flex-row justify-between">
                    <div>Sous-Total</div>
                    <div>{{ $cart->subTotal / 100 }} €</div>
                </div>
                <div class="flex flex-row justify-between">
                    <div>Livraison</div>
                    <div>
                        {{ $cart->shippingTotal !== null ? ($cart->shippingTotal / 100).' €' : 'Gratuite' }}
                    </div>
                </div>
                <div class="flex flex-row justify-between text-base mt-4">
                    <div class="font-semibold">Total TTC</div>
                    <div>{{ $cart->total / 100 }} €</div>
                </div>
            </div>
        </div>
        <div class="mt-6">
            <x-button primary :url="route('checkout')">
                Finaliser la commande
            </x-button>
        </div>
    @endif
</div>
