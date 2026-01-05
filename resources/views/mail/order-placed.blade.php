@php
    /**  @var \Lunar\Models\Order $order */

    $recapUrl = route('order', $order->reference);
@endphp

<x-mail::message>
# Commande #{{ $order->reference }}

Bonjour {{ $order->shippingAddress->first_name }},<br>

Nous avons bien reçu votre commande :

<table style="border: 1px solid; padding: 10px; border-collapse: collapse">
<thead>
<tr>
<th style="border: 1px solid; padding: 10px">Article</th>
<th style="border: 1px solid; padding: 10px">Prix TTC</th>
</tr>
</thead>
<tbody>
@foreach($order->lines as $line)
<tr>
@php /** @var \Lunar\Models\OrderLine $line */ @endphp
<td style="border: 1px solid; padding: 10px">{{ $line->description }} {{ $line->option ? "| {$line->option}" : '' }}</td>
<td style="border: 1px solid; padding: 10px">{{ $line->unit_price->decimal() }}€</td>
</tr>
@endforeach
</tbody>
</table>

<br>

[Voir le récapitulatif]({{ $recapUrl }})

Merci pour votre confiance !<br>
Mathilde Calatrava
</x-mail::message>
