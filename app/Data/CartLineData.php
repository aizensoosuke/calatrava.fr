<?php

namespace App\Data;

use Lunar\Models\CartLine;
use Lunar\Models\ProductOption;
use Spatie\LaravelData\Data;
use Svg\Tag\Image;

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
        $subTotalWithoutTax = $line->unitPrice->decimal();
        $taxTotal = $line->taxAmount?->decimal() ?? 0;
        $subTotal = $subTotalWithoutTax + $taxTotal;

        return new self(
            id: $line->id,
            quantity: $line->quantity,
            price: $subTotal,
            variant: ProductVariantData::fromCartLine($line),
            product: ProductData::fromCartLine($line),
        );
    }
}
