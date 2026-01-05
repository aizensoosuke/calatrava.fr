@php
    /**  @var \Lunar\Models\Order $order */

    $retryUrl = route('retry-payment', $order->reference);
@endphp

<x-mail::message>
# Commande #{{ $order->reference }}

Bonjour {{ $order->shippingAddress->first_name }},<br>

Votre paiement de {{ $order->total->formatted() }} a échoué.

Vos articles vous sont réservés pour deux jours à partir de la création du panier.

[Réessayer le paiement]({{ $retryUrl }})

Merci pour votre confiance !<br>
Mathilde Calatrava
</x-mail::message>
