@props([
    'line' => null,
])

@php
    use App\Data\ProductData;
    use App\Data\ProductVariantData;
    use Lunar\Models\OrderLine;

    /** @var OrderLine $line */
    if ($line->purchasable_type != "product_variant") {
        return;
    }

    $product = ProductData::fromProductVariant($line->purchasable);
@endphp

<div class="flex overflow-hidden gap-2">
    <div class="w-[80px]">
        <img alt="{{ $product->mainImage->alt }}" src="{{ $product->mainImage->url }}"/>
    </div>
    <div class="flex flex-col justify-between items-start w-full">
        <div class="flex flex-col w-full">
            <div class="flex font-bold text-xs justify-between gap-2">
                <div>{{ $product->name }}</div>
                <div class="text-nowrap">{{ $line->unit_price->decimal() }} €</div>
            </div>
            <div class="text-xs">Qté: {{ $line->unit_quantity }}</div>
            <div class="text-xs">Variante: {{ $line->option }}</div>
        </div>
    </div>
</div>
