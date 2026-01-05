<?php

namespace App\PaymentTypes;

use App\Services\CawlPaymentService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Lunar\Base\DataTransferObjects\PaymentCapture;
use Lunar\Base\DataTransferObjects\PaymentRefund;
use Lunar\Base\DataTransferObjects\PaymentAuthorize;
use Lunar\Models\CartLine;
use Lunar\Models\Contracts\Transaction;
use Lunar\Models\Order as LunarOrder;
use Lunar\Models\Transaction as LunarTransaction;
use Lunar\PaymentTypes\AbstractPayment;
use OnlinePayments\Sdk\Domain\AmountOfMoney;
use OnlinePayments\Sdk\Domain\Customer;
use OnlinePayments\Sdk\Domain\LineItem;
use OnlinePayments\Sdk\Domain\Order;
use OnlinePayments\Sdk\Domain\OrderLineDetails;
use OnlinePayments\Sdk\Domain\OrderReferences;
use OnlinePayments\Sdk\Domain\Shipping;
use OnlinePayments\Sdk\Domain\ShoppingCart;

class CawlPayment extends AbstractPayment
{
    public function authorize(): ?PaymentAuthorize
    {
        $lunarOrder = $this->order;
        /** @var Cart $cart */
        $cart = $lunarOrder->cart;
        $cart->calculate();

        $cawlPaymentService = new CawlPaymentService();

        $createHostedCheckoutResponse = $cawlPaymentService
            ->configureOrder(function (Order $order) use ($cart, $lunarOrder) {
                $customer = new Customer();
                $customer->setMerchantCustomerId($cart->shippingAddress()->first()->email);

                $shipping = new Shipping();
                $shipping->setShippingCost($cart->shippingSubTotal->value);

                $shoppingCartItems = $cart->lines->map(function (CartLine $line) {
                    $lineAmountOfMoney = new AmountOfMoney();
                    $lineAmountOfMoney->setAmount($line->unitPrice->value);
                    $lineAmountOfMoney->setCurrencyCode('EUR');

                    $lineDetails = new OrderLineDetails();
                    $lineDetails->setQuantity($line->quantity);
                    $lineDetails->setProductName($line->purchasable->product->name);
                    $lineDetails->setProductCode($line->purchasable->product->sku);
                    $lineDetails->setProductPrice($line->unitPrice->value);

                    $lineItem = new LineItem();
                    $lineItem->setAmountOfMoney($lineAmountOfMoney);
                    $lineItem->setOrderLineDetails($lineDetails);

                    return $lineItem;
                })->all();

                $shoppingCart = new ShoppingCart();
                $shoppingCart->setItems($shoppingCartItems);

                $amountOfMoney = new AmountOfMoney();
                $amountOfMoney->setAmount($cart->total->value);

                $amountOfMoney->setCurrencyCode("EUR");

                $orderReferences = new OrderReferences();
                $orderReferences->setDescriptor(config('env.card_payment_descriptor'));
                $orderReferences->setMerchantReference($lunarOrder->reference);

                $order->setAmountOfMoney($amountOfMoney);
                $order->setShipping($shipping);
                $order->setCustomer($customer);
                $order->setShoppingCart($shoppingCart);
                $order->setReferences($orderReferences);
            })
            ->finalize();

        // TODO: check that there is no pending payment
        $lunarOrder->update([
            'meta' => [
                'RETURNMAC' => $createHostedCheckoutResponse->getRETURNMAC(),
                'hostedCheckoutId' => $createHostedCheckoutResponse->getHostedCheckoutId(),
                'paymentRedirectUrl' => $createHostedCheckoutResponse->getRedirectUrl(),
            ]
        ]);

        return null;
    }

    public function callback(string $returnmac, string $hostedCheckoutId): LunarOrder
    {
        // FIXME: handle exceptions
        $hostedCheckout = app(CawlPaymentService::class)
            ->getHostedCheckout($hostedCheckoutId);

        $paymentId = $hostedCheckout->getCreatedPaymentOutput()->getPayment()->getId();

        $transaction = LunarTransaction::with('order')->firstWhere('reference', $paymentId);
        if($transaction?->exists) {
            Log::debug('User refreshed payment callback page. Found the transaction.', ['paymentId' => $paymentId]);
            return $transaction->order;
        }

        $lunarOrder = LunarOrder::where('meta->RETURNMAC', $returnmac)
            ->where('meta->hostedCheckoutId', $hostedCheckoutId)
            ->first();

        if (is_null($lunarOrder)) {
            // TODO: do something helpful
            throw new \Exception("Could not find order.");
        }

        Cache::lock('write-transactions', 10)->get(function () use ($lunarOrder, $hostedCheckout, $paymentId, $returnmac, $hostedCheckoutId) {
            $transaction = LunarTransaction::firstWhere('reference', $paymentId);
            if ($hostedCheckout->getCreatedPaymentOutput()->getPaymentStatusCategory() === "SUCCESSFUL") {
                if (!$transaction) {
                    Log::debug('/payment-callback: creating successful transaction', ['order' => $lunarOrder->reference, 'paymentId' => $paymentId]);
                    $transaction = $lunarOrder->transactions()->create([
                        'success' => true,
                        'type' => 'capture',
                        'driver' => 'cawl',
                        'amount' => $hostedCheckout->getCreatedPaymentOutput()->getPayment()->getPaymentOutput()->getAmountOfMoney()->getAmount(),
                        'reference' => $paymentId,
                        'status' => 'Paiement réussi',
                        'notes' => '',
                        'card_type' => '',
                        'last_four' => '',
                        'captured_at' => now(),
                        'meta' => [
                            'RETURNMAC' => $returnmac,
                            'hostedCheckoutId' => $hostedCheckoutId,
                        ]
                    ]);
                } else {
                    Log::debug('/payment-callback: updating successful transaction (likely created by webhook)', ['order' => $lunarOrder->reference, 'paymentId' => $paymentId]);
                }

                $transaction->meta["RETURNMAC"] = $returnmac;
                $transaction->meta["hostedCheckoutId"] = $hostedCheckoutId;
                $transaction->save();

                $lunarOrder->update([
                    'status' => 'payment-received',
                    'placed_at' => now(),
                ]);
            } else {
                if (!$transaction) {
                    Log::debug('/payment-callback: creating failed transaction', ['order' => $lunarOrder->reference, 'paymentId' => $paymentId]);
                    $transaction = $lunarOrder->transactions()->create([
                        'success' => false,
                        'type' => 'capture',
                        'driver' => 'cawl',
                        'amount' => $hostedCheckout->getCreatedPaymentOutput()->getPayment()->getPaymentOutput()->getAmountOfMoney()->getAmount(),
                        'reference' => $paymentId,
                        'status' => 'Paiement échoué',
                        'notes' => '',
                        'card_type' => '',
                        'last_four' => '',
                        'captured_at' => now(),
                        'meta' => [
                            'RETURNMAC' => $returnmac,
                            'hostedCheckoutId' => $hostedCheckoutId,
                        ]
                    ]);
                } else {
                    Log::debug('/payment-callback: updating failed transaction (likely created by webhook)', ['order' => $lunarOrder->reference, 'paymentId' => $paymentId]);
                }

                $transaction->meta["RETURNMAC"] = $returnmac;
                $transaction->meta["hostedCheckoutId"] = $hostedCheckoutId;
                $transaction->save();
            }
        });

        return $lunarOrder;
    }

    public function refund(Transaction $transaction, int $amount = 0, $notes = null): PaymentRefund
    {
        return new PaymentRefund(false, "Impossible de rembourser des paiements ici. Il faut aller dans l'interface d'administration Cawl.");
    }

    public function capture(Transaction $transaction, $amount = 0): PaymentCapture
    {
        return new PaymentCapture(false, "Impossible de capturer des paiements manuellement. L'utilisateur doit compléter le paiement de son côté.");
    }
}
