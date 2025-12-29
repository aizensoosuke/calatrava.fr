<?php

namespace App\Data;

use Lunar\Models\CartLine;
use Lunar\Models\ProductOption;
use Lunar\Models\ProductVariant;
use Spatie\LaravelData\Data;

class ProductVariantData extends Data
{
    public function __construct(
        public string $id,
        public string $size,
        public string $color,
        public int $stock = 0,
    ) {}

    public static function fromCartLine(CartLine $cartLine): self
    {
        return self::fromProductVariant($cartLine->purchasable);
    }

    public static function fromProductVariant(ProductVariant $variant): self
    {
        $size_option_id = ProductOption::firstWhere('handle', 'size')->id;
        $color_option_id = ProductOption::firstWhere('handle', 'color')->id;

        return new self(
            id:  $variant->id,
            size: $variant->values->where('product_option_id', $size_option_id)->first()?->translate('name') ?? '',
            color: $variant->values->where('product_option_id', $color_option_id)->first()?->translate('name') ?? '',
            stock: $variant->stock
        );
    }
}
