<?php

namespace App\Data;

use Illuminate\Support\Collection;
use Livewire\Wireable;
use Lunar\Models\Product;
use Lunar\Models\ProductOptionValue;
use Spatie\LaravelData\Concerns\WireableData;
use Spatie\LaravelData\Data;

class ProductOptionData extends Data implements Wireable
{
    use WireableData;

    public function __construct(
        public int $id,
        public string $name,
        public int $stock,
    ) {}

    public static function fromProductOptionValue(ProductOptionValue $productOptionValue): self
    {
        return new self(
            id: $productOptionValue->id,
            name: $productOptionValue->name->fr,
            stock: 1
        );
    }

    /**
     * @param Product $product
     * @return Collection<int, self>
     */
    public static function collectFromProduct(Product $product, string $handle): Collection
    {
        $product->load(['variants', 'variants.values']);
        return $product->variants
            ->map->values->flatten()
            ->where('option.handle', $handle)
            ->unique('id')
            ->map(fn (ProductOptionValue $productOptionValue) => self::fromProductOptionValue($productOptionValue));
    }
}
