@props([
    'line' => null,
    'nodelete' => null,
])

@php
use App\Data\CartLineData;
/** @var CartLineData $line */
@endphp

<div class="flex overflow-hidden gap-2">
    <div class="w-[80px]">
        <img alt="{{ $line->product->mainImage->alt }}" src="{{ $line->product->mainImage->url }}" />
    </div>
    <div class="flex flex-col justify-between items-start w-full">
        <div class="flex flex-col w-full">
            <div class="flex font-bold text-xs justify-between gap-2">
                <div>{{ $line->product->name }}</div>
                <div class="text-nowrap">{{ $line->price }} €</div>
            </div>
            <div class="text-xs">Qté: {{ $line->quantity }}</div>
            <div class="text-xs">Taille: {{ $line->variant->size }}</div>
            <div class="text-xs">Couleur: {{ $line->variant->color }}</div>
        </div>
        @if($nodelete === null || $nodelete === false)
            <div
                class="text-xs cursor-pointer hover:underline"
                wire:click="delete({{ $line->id }})"
            >Supprimer</div>
        @endif
    </div>
</div>
