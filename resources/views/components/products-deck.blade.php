@props([
    'products' => null
])

<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-0.5">
    @foreach($products as $product)
        <livewire:product-card :$product />
    @endforeach
</div>
