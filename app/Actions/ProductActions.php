<?php

namespace App\Actions;

use App\Models\Product;
use Illuminate\Support\Collection;
use Lunar\Models\Collection as LunarCollection;
use App\Data\ProductData;

class ProductActions
{
    public static function getProductsFromCollection(string $slug): Collection
    {
        $url = LunarActions::fetchUrl(
            $slug,
            LunarCollection::morphName(),
            [
                'element.thumbnail',
                'element.products.variants.basePrices',
                'element.products.defaultUrl',
            ]
        );

        if (! $url) {
            abort(404);
        }

        $collection = $url->element;

        $productModels = $collection->products()
            ->get();

        return $productModels->map(fn ($product) => ProductData::fromProductModel($product));
    }

    public static function getProductFromSlug(string $slug): ProductData
    {
        $url = LunarActions::fetchUrl(
            $slug,
            \Lunar\Models\Product::morphName(),
        );

        if (! $url) {
            abort(404);
        }

        /** @var Product $product */
        $product = $url->element;

        return ProductData::fromProductModel($product);
    }

    public static function getProducts(): Collection
    {
        $productModels = Product::with([
            'thumbnail',
            'variants.basePrices',
            'prices.currency',
            'prices.priceable',
            'variants.values.option',
            'media',
        ])->get();

        return $productModels->map(
            fn ($product) => ProductData::from($product)
        )->flatten();
    }
}
