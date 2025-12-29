<?php

namespace App\Models;

use App\Data\ImageData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends \Lunar\Models\Product
{
    public function getName(): string
    {
        return $this->translateAttribute('name');
    }

    public function getDescription(): ?string
    {
        return $this->translateAttribute('description');
    }

    public function getPrice(): float
    {
        return $this->prices
            ->first()
            ->price
            ->decimal();
    }

    public function getImages(): Collection
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

    public function getProductOptionValues(): Collection
    {
        return $this->variants->pluck('values')->flatten();
    }

    public function getProductOptions(): Collection
    {
        return $this->productOptionValues->unique('id')->groupBy('product_option_id')
            ->map(function ($values) {
                return [
                    'option' => $values->first()->option,
                    'values' => $values,
                ];
            })->values();
    }

    public function getSizes(): Collection
    {
        $sizes = $this->productOptions->where('option.handle', 'size')->first();
        if (! $sizes) {
            return collect();
        }

        return collect($sizes['values'])->sortBy('position')->flatten();
    }

    public function getIsInStock(): bool
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
