@php
use Lunar\Facades\CartSession;
use App\Data\CartData;

$cartSession = CartSession::current();
if ($cartSession) {
    $cart = CartData::from($cartSession);
}
@endphp

<x-layouts.app>
    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <div class="flex flex-row gap-6 items-start">
                <livewire:address />

                <div class="bg-white p-4 shadow sm:p-8 max-w-sm">
                    <h2 class="text-lg font-medium text-gray-900">
                        Récapitulatif de votre commande
                    </h2>

                    @if($cart === null || $cart->lines->count() == 0)
                        <div class="text-center">Votre panier est vide</div>
                    @else
                        <div class="flex flex-col gap-2 mt-6">
                            @foreach($cart->lines as $line)
                                <div wire:key="{{ $line->variant->id }}">
                                    <x-cart-line :$line nodelete />
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-6 text-sm">
                            <div class="flex flex-col">
                                @if($cart->subTotal !== null)
                                    <div class="flex flex-row justify-between">
                                        <div>Sous-Total</div>
                                        <div>{{ $cart->subTotal / 100 }} €</div>
                                    </div>
                                @endif
                                <div class="flex flex-row justify-between">
                                    <div>Livraison</div>
                                    <div>
                                        {{ $cart->shippingTotal !== null ? ($cart->shippingTotal / 100).' €' : 'Gratuite' }}
                                    </div>
                                </div>
                                <div class="flex flex-row justify-between text-base mt-4">
                                    <div class="font-semibold">Total</div>
                                    <div>{{ $cart->total / 100 }} €</div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
