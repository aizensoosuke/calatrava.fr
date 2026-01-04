<?php
use App\Actions\CheckoutAction;
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
    'name' => auth()->user()?->name ?? '',
    'address' => '',
    'address2' => '',
    'postalCode' => '',
    'city' => ''
])->modelable();

$saveAddressAndRedirectToPayment = action(function () {
    $createHostedCheckoutResponse = CheckoutAction::execute();
    if ($createHostedCheckoutResponse->getInvalidTokens() !== null)
    {
        dd($createHostedCheckoutResponse);
    }
    return redirect($createHostedCheckoutResponse->getRedirectUrl());
});
?>


<div class="bg-white p-4 shadow sm:p-8 grow">
    <h2 class="text-lg font-medium text-gray-900">
        Votre adresse de livraison
    </h2>
    <div class="mt-2 text-sm text-gray-600">
        Nous livrons uniquement en France métropolitaine.
    </div>
    <form class="mt-6 space-y-6">
        <!-- Name -->
        <div>
            <label class='block font-medium text-sm text-gray-700' for="name">
                Nom complet
            </label>
            <input
                wire:model="name"
                id="name"
                class="px-3 py-2 block mt-1 w-full border border-gray-300 focus:ring-indigo-500"
                type="text"
                name="name"
                required
                autofocus
                autocomplete="name"/>
            @if($errors->get('name'))
                <ul class='text-sm text-red-600 space-y-1 mt-2'>
                    @foreach ((array) $errors->get('name') as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            @endif
        </div>

        <!-- Address -->
        <div>
            <label class='block font-medium text-sm text-gray-700' for="address">
                Adresse
            </label>
            <input
                wire:model="address"
                id="address"
                class="px-3 py-2 block mt-1 w-full border border-gray-300 focus:ring-indigo-500"
                type="text"
                name="address"
                required
                autofocus
                autocomplete="street-address"/>
            @if($errors->get('address'))
                <ul class='text-sm text-red-600 space-y-1 mt-2'>
                    @foreach ((array) $errors->get('address') as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            @endif
        </div>

        <!-- Appartement, bâtiment ou autre-->
        <div x-data="{showAddress2: false}">
            <div
                class="text-sm text-gray-700 hover:underline cursor-pointer"
                x-show="! showAddress2"
                x-on:click="showAddress2 = true"
            >
                + Ajouter un appartement, bâtiment ou autre
            </div>
            <div x-cloak x-show="showAddress2">
                <label class='block font-medium text-sm text-gray-700' for="address2">
                    Appartement, bâtiment ou autre <span class="text-gray-700">(optionel)</span>
                </label>
                <input
                    wire:model="address2"
                    id="address2"
                    class="px-3 py-2 block mt-1 w-full border border-gray-300 focus:ring-indigo-500"
                    type="text"
                    name="address2"
                    autofocus
                    autocomplete="street-address-2"/>
                @if($errors->get('address2'))
                    <ul class='text-sm text-red-600 space-y-1 mt-2'>
                        @foreach ((array) $errors->get('address2') as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <!-- Postal code -->
        <div>
            <label class='block font-medium text-sm text-gray-700' for="postal-code">
                Code postal
            </label>
            <input
                wire:model="postalCode"
                id="postal-code"
                class="px-3 py-2 block mt-1 w-full border border-gray-300 focus:ring-indigo-500"
                type="text"
                name="postal-code"
                required
                autofocus
                autocomplete="postal-code"/>
            @if($errors->get('postal-code'))
                <ul class='text-sm text-red-600 space-y-1 mt-2'>
                    @foreach ((array) $errors->get('postal-code') as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            @endif
        </div>

        <!-- Ville -->
        <div>
            <label class='block font-medium text-sm text-gray-700' for="city">
                Ville
            </label>
            <input
                wire:model="city"
                id="city"
                class="px-3 py-2 block mt-1 w-full border border-gray-300 focus:ring-indigo-500"
                type="text"
                name="city"
                required
                autofocus
                autocomplete="city"/>
            @if($errors->get('city'))
                <ul class='text-sm text-red-600 space-y-1 mt-2'>
                    @foreach ((array) $errors->get('city') as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    </form>
    <div class="mt-7">
        <span x-on:click.prevent="$wire.saveAddressAndRedirectToPayment()">
            <x-button primary>
                Payer la commande
            </x-button>
        </span>
    </div>
</div>
