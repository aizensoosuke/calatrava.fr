@props([
    'slug' => ''
])

@php
    use App\Actions\ProductActions;

    $product = ProductActions::getProductFromSlug($slug);
@endphp

<x-layouts.app>
    <div class="flex items-start">
        <div class="max-w-[600px]">
            <livewire:product-card class="invisible" :$product />
        </div>
        <div class="bg-white p-4 shadow sm:p-8 mx-6 mb-0 w-full">
            <h2 class="text-2xl font-medium">{{ $product->name }}</h2>
            <div class="markdown">
                {!! $product->htmlDescription !!}
            </div>
        </div>
    </div>
</x-layouts.app>
