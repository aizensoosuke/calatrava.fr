<?php

namespace App\Data;

use Illuminate\Support\Collection;
use Livewire\Wireable;
use Lunar\Models\Cart;
use Lunar\Models\CartLine;
use Spatie\LaravelData\Concerns\WireableData;
use Spatie\LaravelData\Data;

class CartData extends Data implements Wireable
{
    use WireableData;

    public function __construct(
        /** @var Collection<CartLineData> */
        public Collection $lines,
        public ?int $total,
        public ?int $subTotal,
        public ?int $shippingTotal,
    )
    {
    }

    public static function fromModel(Cart $cart): self
    {
        $cart->calculate();

        $subTotal = $cart->subTotal?->value ?? 0;
        $shippingTotal = $subTotal > 10000 ? 0 : 500;
        $total = ($cart->total?->value ?? 0) + $shippingTotal;

        return new self(
            lines: $cart->lines->map(fn (CartLine $line) => CartLineData::fromModel($line)),
            total: $total,
            subTotal: $subTotal,
            shippingTotal: $shippingTotal,
        );
    }
}
