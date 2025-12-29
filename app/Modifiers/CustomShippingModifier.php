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
        $taxClass = TaxClass::default()->first();

        $shippingPriceValue = (int)config('env.shipping.fees');

        ShippingManifest::addOptions(collect([
            new ShippingOption(
                name: 'Livraison simple',
                description: 'Livraison simple',
                identifier: 'SHIPPING',
                price: new Price($shippingPriceValue, $cart->currency, 1),
                taxClass: $taxClass
            ),
            new ShippingOption(
                name: 'Livraison simple offerte',
                description: 'Livraison simple offerte',
                identifier: 'SHIPPING_FREE',
                price: new Price(0, $cart->currency, 1),
                taxClass: $taxClass
            )
        ]));

        return $next($cart);
    }
}
