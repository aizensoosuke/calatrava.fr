<?php

namespace App\Actions;

use Lunar\Models\Url as UrlModel;

class LunarActions
{
    public static function fetchUrl($slug, $type, $eagerLoad = []): ?UrlModel
    {
        return UrlModel::whereElementType($type)
            ->whereDefault(true)
            ->whereSlug($slug)
            ->with($eagerLoad)->first();
    }
}
