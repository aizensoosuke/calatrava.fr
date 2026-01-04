<?php

namespace App\Actions;

use App\Data\LunarCollectionData;
use Illuminate\Support\Collection;
use Lunar\Models\Collection as CollectionModel;

class NavigationActions
{
    /** @return Collection<int, LunarCollectionData> */
    public static function makeNavigationData(): Collection
    {

        $collections = CollectionModel::with(['defaultUrl'])->orderBy('_lft')->get();
        $roots = $collections->whereNull('parent_id');

        return $roots->map(fn($root) => LunarCollectionData::make($root, $collections))->flatten();
    }
}
