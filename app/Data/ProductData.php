<?php

namespace App\Data;

use App\Models\Product;
use Illuminate\Support\Collection;
use Livewire\Wireable;
use Lunar\Models\CartLine;
use Lunar\Models\ProductVariant;
use Spatie\LaravelData\Concerns\WireableData;
use Spatie\LaravelData\Data;

class ProductData extends Data implements Wireable
{
    use WireableData;

    public function __construct(
        public string       $id,
        public string       $name,
        public string       $htmlDescription,
        public string       $price,
        public bool $isAvailable,
        public ?ImageData $mainImage,
        /** @var Collection<int, ImageData> $carousel */
        public Collection $carousel,
        /** @var Collection<int, ProductVariantData> $variants */
        public Collection   $variants,
        /** @var Collection<int, ImageData> $thumbnails */
        public Collection $thumbnails,
    ) {}

    public static function fromProductModel(Product $product): self
    {
        return new self(
            id: $product->id,
            name: $product->name,
            htmlDescription: $product->description ?? '',
            price: $product->price,
            isAvailable: $product->isInStock,
            mainImage: $product->images->first(),
            carousel: $product->images,
            variants: $product->variants->map(
                fn ($variant) => ProductVariantData::fromProductVariant($variant)
            ),
            thumbnails: $product->imagesWithConversion('small')
        );
    }

    public static function fromProductVariant(ProductVariant $variant): self
    {
        return self::fromProductModel($variant->product);
    }

    public static function fromCartLine(CartLine $line): self
    {
        return self::fromProductVariant($line->purchasable);
    }
}
