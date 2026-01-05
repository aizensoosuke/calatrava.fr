@php
    /**  @var \Lunar\Models\Order $order */

    use Lunar\Admin\Filament\Resources\OrderResource;

    $lunarOrderUrl = OrderResource::getUrl('order', ['record' => $order->id]);
    $orderUrl = route('order', $order->reference);
@endphp

<x-mail::message>
# Commande #{{ $order->reference }}

Une nouvelle commande a été passée.

@foreach($order->lines as $line)
@php /** @var \Lunar\Models\OrderLine $line */ @endphp
- {{ $line->description }} | {{ $line->option }} | {{ $line->unit_price->decimal() }}€
@endforeach

[Voir le récapitulatif de commande]({{ $orderUrl }})

[Voir la commande sur Lunar]({{ $lunarOrderUrl }})
</x-mail::message>
