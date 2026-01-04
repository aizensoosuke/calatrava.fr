@php
    use \App\Actions\ProductActions;

    $products = ProductActions::getProducts();
@endphp

<x-layouts.app>
    <x-products-deck :$products />
</x-layouts.app>
