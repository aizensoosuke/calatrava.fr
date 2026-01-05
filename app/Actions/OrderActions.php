<?php

namespace App\Actions;

use App\PaymentTypes\CawlPayment;
use Illuminate\Support\Facades\URL;
use Lunar\Facades\Payments;
use Lunar\Facades\ShippingManifest;
use Lunar\Models\Cart;
use Lunar\Models\Order;
use Lunar\Models\Order as LunarOrder;
use Lunar\Models\Url as UrlModel;

class OrderActions
{
    public static function createOrder(Cart $cart): Order {
        $freeShipping = $cart->total->value > config('env.shipping.free_from');
        $shippingOptionId = $freeShipping ? 'SHIPPING_FREE' : 'SHIPPING';
        $shippingOption = ShippingManifest::getOption($cart, $shippingOptionId);
        $cart->setShippingOption($shippingOption);

        $cart->calculate();
        $lunarOrder = $cart->createOrder();

        // FIXME: unlikely crash if reference is not unique
        $ref = str(str()->uuid())->substr(0, 8)->upper();
        $lunarOrder->update([
            'customer_id' => $cart->customer_id,
            'reference' => $ref,
            'customer_reference' => $ref,
        ]);

        return $lunarOrder;
    }

    public static function createPaymentPage(Order $order): string
    {
        /** @var CawlPayment $cawl */
        $cawl = Payments::driver('cawl');
        $cawl->order($order);
        $cawl->authorize();

        $order->refresh();
        return $order->meta->paymentRedirectUrl;
    }
}
