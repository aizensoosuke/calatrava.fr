<?php

namespace App\Actions;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Lunar\Models\Cart;

class CheckoutActions
{
    public static function checkout(Cart $cart): RedirectResponse|Redirector
    {
        $order = OrderActions::createOrder($cart);
        $paymentUrl = OrderActions::createPaymentPage($order);
        return redirect($paymentUrl);
    }
}
