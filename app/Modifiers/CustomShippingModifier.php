<?php

namespace App\Modifiers;

use Lunar\Base\ShippingModifier;
use Lunar\DataTypes\Price;
use Lunar\DataTypes\ShippingOption;
use Lunar\Facades\ShippingManifest;
use Lunar\Models\Contracts\Cart;
use Lunar\Models\Currency;
use Lunar\Models\TaxClass;

class CustomShippingModifier extends ShippingModifier
{
    public function handle(Cart $cart, \Closure $next)
    {
        $taxClass = TaxClass::first();

        ShippingManifest::addOptions(collect([
            new ShippingOption(
                name: 'Chronopost',
                description: 'Livraison Chronopost simple',
                identifier: 'CHRONOPOST',
                price: new Price(500, $cart->currency, 1),
                taxClass: $taxClass
            ),
            new ShippingOption(
                name: 'Chronopost Offert',
                description: 'Livraison Chronopost simple offerte',
                identifier: 'CHRONOPOST_FREE',
                price: new Price(0, $cart->currency, 1),
                taxClass: $taxClass
            )
        ]));

        return $next($cart);
    }
}
