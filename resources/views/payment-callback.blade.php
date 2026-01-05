@php
    use App\Actions\OrderActions;use App\PaymentTypes\CawlPayment;use App\Services\CawlPaymentService;
    use Lunar\Facades\Payments;

    $returnmac = request()->query('RETURNMAC');
    $hostedCheckoutId = request()->query('hostedCheckoutId');

    /** @var CawlPayment $cawl */
    $cawl = Payments::driver('cawl');
    $lunarOrder = $cawl->callback($returnmac, $hostedCheckoutId);
    $success = $lunarOrder->isPlaced();

    if(!$success) {
        $paymentRetryUrl = OrderActions::createPaymentPage($lunarOrder);
    }
@endphp

<x-layouts.app>
    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <div class="flex flex-row gap-6 items-start">
                <div class="bg-white p-4 shadow sm:p-8 w-full space-y-4">
                    <h1 class="text-xl">Commande #{{ $lunarOrder->reference }}</h1>
                    <h2 class="text-lg font-medium text-gray-900">
                        @if($success)
                            ✅ Paiement réussi ! <br>
                            <small>Un reçu vous a été envoyé par email à
                                l'adresse {{ $lunarOrder->shippingAddress->contact_email }}.</small>
                        @else
                            ❌ Paiement échoué !
                        @endif
                    </h2>
                    @if($success)
                        <x-button primary :url="route('order', $lunarOrder->reference)">
                            Votre récapitulatif de commande
                        </x-button>
                    @else
                        <x-button primary :url="$paymentRetryUrl">
                            Réessayer le paiement
                        </x-button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
