@props([
    'slug' => ''
])

@php
    use App\Actions\ProductActions;

    $products = ProductActions::getProductsFromCollection($slug);
@endphp

<x-layouts.app>
    <x-products-deck :$products/>
</x-layouts.app>
