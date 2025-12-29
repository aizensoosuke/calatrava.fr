<?php

namespace App\Data;

use Lunar\Models\CartLine;
use Spatie\LaravelData\Data;

class CartLineData extends Data
{
    public function __construct(
        public int $id,
        public int $quantity,
        public float $price,
        public ProductVariantData $variant,
        public ProductData $product,
    )
    {
    }

    public static function fromModel(CartLine $line): self
    {
        return new self(
            id: $line->id,
            quantity: $line->quantity,
            price: $line->unitPriceInclTax->decimal(),
            variant: ProductVariantData::fromCartLine($line),
            product: ProductData::fromCartLine($line),
        );
    }
}
