@php
    use App\Services\CawlPaymentService;
    use Lunar\Facades\Payments;

    $returnmac = request()->query('RETURNMAC');
    $hostedCheckoutId = request()->query('hostedCheckoutId');

    $cawl = Payments::driver('cawl');
    $success = $cawl->callback($returnmac, $hostedCheckoutId);
@endphp

<x-layouts.app>
    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <div class="flex flex-row gap-6 items-start">
                <div class="bg-white p-4 shadow sm:p-8 w-full">
                    <h2 class="text-lg font-medium text-gray-900">
                        @if($success)
                            Paiement effectué !
                        @else
                            Paiement échoué !
                        @endif
                    </h2>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
