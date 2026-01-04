<?php

namespace App\Providers;

use App\Models\Product;
use App\Modifiers\CustomShippingModifier;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Lunar\Admin\Support\Facades\LunarPanel;
use Lunar\Facades\ModelManifest;
use Lunar\Models\Contracts\Product as LunarProduct;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        LunarPanel::register();

        ModelManifest::replace(
            LunarProduct::class,
            Product::class,
        );
    }

    public function boot(\Lunar\Base\ShippingModifiers $shippingModifiers): void
    {
        URL::forceScheme('https');

        $shippingModifiers->add(
            CustomShippingModifier::class
        );
    }
}
