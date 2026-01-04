<?php

namespace App\Models;

use App\Data\ImageData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends \Lunar\Models\Product
{
    public function getNameAttribute(): string
    {
        return $this->translateAttribute('name');
    }

    public function getDescriptionAttribute(): ?string
    {
        return $this->translateAttribute('description');
    }

    public function getPriceAttribute(): float
    {
        return $this->prices
            ->first()
            ->price
            ->decimal();
    }

    public function getImagesAttribute(): Collection
    {
        return $this->getMedia('images')->map(function (Media $image) {
            return ImageData::from([
                'url' => $image->getUrl('card'),
                'alt' => $this->translateAttribute('name'),
            ]);
        });
    }

    public function imagesWithConversion(string $conversionName): Collection
    {
        return $this->getMedia('images')->map(function (Media $image) use ($conversionName) {
            return ImageData::from([
                'url' => $image->getUrl($conversionName),
                'alt' => $this->translateAttribute('name'),
            ]);
        });
    }

    public function getProductOptionValuesAttribute(): Collection
    {
        return $this->variants->pluck('values')->flatten();
    }

    public function getProductOptionsAttribute(): Collection
    {
        return $this->productOptionValues->unique('id')->groupBy('product_option_id')
            ->map(function ($values) {
                return [
                    'option' => $values->first()->option,
                    'values' => $values,
                ];
            })->values();
    }

    public function getSizesAttribute(): Collection
    {
        $sizes = $this->productOptions->where('option.handle', 'size')->first();
        if (! $sizes) {
            return collect();
        }

        return collect($sizes['values'])->sortBy('position')->flatten();
    }

    public function getIsInStockAttribute(): bool
    {
        return $this->variants()->where('stock', '>', 0)->exists();
    }

    public function scopeInStock(Builder $query): void
    {
        $query->whereHas('variants', function ($query) {
            $query->where('stock', '>', 0);
        })->get();
    }
}
