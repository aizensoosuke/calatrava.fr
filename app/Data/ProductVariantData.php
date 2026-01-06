<?php

namespace App\Data;

use Livewire\Wireable;
use Lunar\Models\CartLine;
use Lunar\Models\ProductOption;
use Lunar\Models\ProductVariant;
use Spatie\LaravelData\Concerns\WireableData;
use Spatie\LaravelData\Data;

class ProductVariantData extends Data implements Wireable
{
    use WireableData;

    public function __construct(
        public string $id,
        public string $sizeId,
        public string $size,
        public string $colorId,
        public string $color,
        public int $stock = 0,
    ) {}

    public static function fromCartLine(CartLine $cartLine): self
    {
        return self::fromProductVariant($cartLine->purchasable);
    }

    public static function fromProductVariant(ProductVariant $variant): self
    {
        $size = $variant->values->flatten()->where('option.handle', 'size')->first();
        $color = $variant->values->flatten()->where('option.handle', 'color')->first();

        return new self(
            id:  $variant->id,
            sizeId: $size?->id,
            size: $size?->name->fr ?? '',
            colorId: $color?->id,
            color: $color?->name->fr ?? '',
            stock: $variant->stock,
        );
    }
}
