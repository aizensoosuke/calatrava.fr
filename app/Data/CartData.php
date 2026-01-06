<?php

namespace App\Data;

use Illuminate\Support\Collection;
use Livewire\Wireable;
use Lunar\Base\Addressable;
use Lunar\Base\ShippingManifest;
use Lunar\DataTypes\ShippingOption;
use Lunar\Models\Cart;
use Lunar\Models\CartLine;
use Lunar\DataTypes\Price;
use Lunar\Models\Country;
use Lunar\Models\Currency;
use Spatie\LaravelData\Concerns\WireableData;
use Spatie\LaravelData\Data;

class CartData extends Data implements Wireable
{
    use WireableData;

    public function __construct(
        /** @var Collection<CartLineData> */
        public Collection $lines,
        public ?int $subTotal,
        public ?int $shippingTotal,
        public ?int $tax,
        public ?int $total,
    )
    {
    }

    public static function fromCart(Cart $cart): self
    {
        $cart->setShippingAddress([
            'country_id' => Country::where('iso2', 'FR')->first()->id,
        ]);
        $cart->calculate();

        $normalShipping = \Lunar\Facades\ShippingManifest::getOption($cart, 'SHIPPING');
        $freeShipping = \Lunar\Facades\ShippingManifest::getOption($cart, 'SHIPPING_FREE');
        $shippingOption = $cart->subTotal->value > config('env.shipping.free_from') ? $freeShipping : $normalShipping;
        $cart->shippingOptionOverride = $shippingOption;
        $cart->setShippingOption($shippingOption);

        $cart->recalculate();

        return new self(
            lines: $cart->lines->map(fn (CartLine $line) => CartLineData::fromModel($line)),
            subTotal: $cart->subTotal->value,
            shippingTotal: $cart->shippingTotal->value,
            tax: $cart->taxTotal->value,
            total: $cart->total->value,
        );
    }

    public static function fromModel(Cart $cart): self {
        return self::fromCart($cart);
    }
}
