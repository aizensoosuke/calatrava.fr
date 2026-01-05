@props([
    'ref' => null
])

@php
    use Lunar\Models\Order;

    $order = Order::firstWhere('reference', $ref);
    $paid = $order->isPlaced();
@endphp

<x-layouts.app>
    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <div class="flex flex-row gap-6 items-start">
                <div class="bg-white p-4 shadow sm:p-8 w-full space-y-4">
                    <div>
                        @if(empty($order))
                            <h1 class="text-xl">
                                ❌ Commande introuvable
                            </h1>
                        @else
                            <div class="flex flex-row items-center justify-between w-full">
                                <h1 class="text-xl">
                                    Commande #{{ $order->reference }}
                                </h1>
                                <div>
                                    @if($paid)
                                        ✅ Commande payée
                                    @else
                                        ❌ Commande impayée
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                    @if(!empty($order))
                        <h2 class="text-lg font-medium text-gray-900">
                            Récapitulatif de votre commande

                            <div class="flex flex-col gap-2 mt-6">
                                @foreach($order->lines as $line)
                                    <div wire:key="{{ $line->id }}">
                                        <x-order-line :$line />
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-6 text-sm">
                                <div class="flex flex-col">
                                    <div class="flex flex-row justify-between">
                                        <div>Sous-Total</div>
                                        <div>{{ $order->sub_total->decimal() }} €</div>
                                    </div>
                                    <div class="flex flex-row justify-between">
                                        <div>Livraison</div>
                                        <div>
                                            {{ $order->shipping_total->value == 0 ? 'Gratuite' : $order->shipping_total->decimal().' €' }}
                                        </div>
                                    </div>
                                    <div class="flex flex-row justify-between text-base mt-4">
                                        <div class="font-semibold">Total</div>
                                        <div>{{ $order->total->decimal() }} €</div>
                                    </div>
                                </div>
                            </div>
                        </h2>
                    @endif
                    <x-button class="mt-2" :url="route('home')" primary>Retour au site</x-button>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
