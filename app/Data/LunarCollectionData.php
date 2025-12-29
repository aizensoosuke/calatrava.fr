<?php

namespace App\Data;

use Illuminate\Support\Collection;
use Lunar\Models\Collection as CollectionModel;
use Spatie\LaravelData\Data;

class LunarCollectionData extends Data
{
    public function __construct(
        public string $slug,
        public string $name,
        public string $url,
        /** @var Collection<self> $children */
        public Collection $children,
        public ImageData $illustration,
    ) {}

    public static function make(CollectionModel $root, $all): self
    {

        $media = $root->media->first();
        $illustration = ImageData::from([
            'url' => $media?->getUrl() ?? '',
            'alt' => $root->translateAttribute('name'),
        ]);

        return new self(
            slug: $root->defaultUrl->slug,
            name: $root->translateAttribute('name'),
            url: route('collection', ['slug' => $root->defaultUrl->slug]),
            children: $all->filter(fn ($item) => $item->parent_id === $root->id)
                ->map(fn ($item) => self::make($item, $all))
                ->flatten(),
            illustration: $illustration,
        );
    }
}
