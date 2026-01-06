<?php

use App\Actions\CheckoutActions;
use App\Data\CartData;
use Lunar\Facades\CartSession;
use Lunar\Models\Address;
use Lunar\Models\CartLine;
use Lunar\Models\Country;
use Lunar\Models\Customer;
use Lunar\Models\TaxZone;

use function Livewire\Volt\protect;
use function Livewire\Volt\state;
use function Livewire\Volt\mount;
use function Livewire\Volt\action;
use function Livewire\Volt\on;

state([
    'firstname' => '',
    'lastname' => '',
    'email' => '',
    'address' => '',
    'address2' => '',
    'postalCode' => '',
    'city' => ''
])->modelable();

$saveAddressAndRedirectToPayment = action(function () {
    $cart = CartSession::current();

    if (!$cart) {
        return null; // TODO: notify user
    }

    $customer = Customer::firstWhere('account_ref', $this->email);

    if (is_null($customer)) {
        $customer = Customer::create([
            'first_name' => $this->firstname,
            'last_name' => $this->lastname,
            'account_ref' => $this->email,
        ]);
    }

    $cart->update(['customer_id' => $customer->id]);

    $address = Address::create([
        'customer_id' => $customer->id,
        'first_name' => $this->firstname,
        'last_name' => $this->lastname,
        'line_one' => $this->address,
        'line_two' => $this->address2,
        'city' => $this->city,
        'postcode' => $this->postalCode,
        'country_id' => Country::firstWhere('iso2', 'FR')->id,
        'delivery_instructions' => '',
        'contact_email' => $this->email,
        'contact_phone' => '',
    ]);

    $cart->setShippingAddress($address);
    $cart->setBillingAddress($address);

    return CheckoutActions::checkout($cart);
});
?>


<div class="bg-white p-4 shadow sm:p-8 grow w-full lg:w-auto">
    <h2 class="text-lg font-medium text-gray-900">
        Votre adresse de livraison
    </h2>
    <div class="mt-2 text-sm text-gray-600">
        Nous livrons uniquement en France métropolitaine.
    </div>
    <form class="mt-6 space-y-6">
        <!-- First Name -->
        <div>
            <label class='block font-medium text-sm text-gray-700' for="firstname">
                Prénom
            </label>
            <input
                wire:model="firstname"
                id="firstname"
                class="px-3 py-2 block mt-1 w-full border border-gray-300 focus:ring-indigo-500"
                type="text"
                name="firstname"
                required
                autofocus
                autocomplete="firstname"/>
            @if($errors->get('firstname'))
                <ul class='text-sm text-red-600 space-y-1 mt-2'>
                    @foreach ((array) $errors->get('firstname') as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            @endif
        </div>

        <!-- Last name -->
        <div>
            <label class='block font-medium text-sm text-gray-700' for="lastname">
                Nom de famille
            </label>
            <input
                wire:model="lastname"
                id="lastname"
                class="px-3 py-2 block mt-1 w-full border border-gray-300 focus:ring-indigo-500"
                type="text"
                name="lastname"
                required
                autocomplete="lastname"/>
            @if($errors->get('lastname'))
                <ul class='text-sm text-red-600 space-y-1 mt-2'>
                    @foreach ((array) $errors->get('lastname') as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            @endif
        </div>

        <!-- Email -->
        <div>
            <label class='block font-medium text-sm text-gray-700' for="email">
                Email
                <small>Un reçu sera envoyé à cet email</small>
            </label>
            <input
                wire:model="email"
                id="email"
                class="px-3 py-2 block mt-1 w-full border border-gray-300 focus:ring-indigo-500"
                type="text"
                name="email"
                required
                autocomplete="email"/>
            @if($errors->get('email'))
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
                autocomplete="city"/>
            @if($errors->get('city'))
                <ul class='text-sm text-red-600 space-y-1 mt-2'>
                    @foreach ((array) $errors->get('city') as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            @endif
        </div>

        <!-- Pays -->
        <div>
            <label class='block font-medium text-sm text-gray-700' for="Pays">
                Pays
            </label>
            <input
                id="country"
                class="px-3 py-2 block mt-1 w-full border border-gray-300 bg-gray-100 focus:ring-indigo-500"
                type="text"
                name="country"
                disabled
                value="France"
            />
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
