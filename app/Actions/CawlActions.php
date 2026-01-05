<?php

namespace App\Actions;

use App\Mail\OrderPlaced;
use App\Mail\OrderPlacedMathilde;
use App\Mail\PaymentFailed;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Lunar\Models\Order;
use Lunar\Models\Transaction;
use OnlinePayments\Sdk\Domain\WebhooksEvent;

class CawlActions
{
    public static function verifyWebhookSignature(string $keyid, string $signature, string $body): bool
    {
        if (empty($keyid) || empty($signature)) {
            return false;
        }

        if ($keyid !== (string)config('cawl.webhook_id')) {
            return false;
        }

        $secretKey = config('cawl.webhook_secret');
        $expectedSignature = base64_encode(hash_hmac("sha256", $body, $secretKey, true));

        if (!hash_equals($signature, $expectedSignature)) {
            return false;
        }

        return true;
    }


    public static function handlePendingCapture(Transaction $transaction, WebhooksEvent $event): void
    {
        $cardNumber = $event->getPayment()->getPaymentOutput()->getCardPaymentMethodSpecificOutput()->getCard()->getCardNumber() ?? '';
        $transaction->fill([
            'success' => true,
            'type' => 'capture',
            'driver' => 'cawl',
            'amount' => $event->getPayment()->getPaymentOutput()->getAmountOfMoney()->getAmount(),
            'status' => 'Paiement réussi',
            'notes' => '',
            'card_type' => $event->getPayment()->getPaymentOutput()->getCardPaymentMethodSpecificOutput()->getAcquirerInformation()->getName() ?? '',
            'last_four' => str($cardNumber)->substr(-4),
            'captured_at' => now(),
        ]);
        $transaction->save();

        $transaction->load('order');
        $transaction->order->update([
            'status' => 'payment-received',
            'placed_at' => now(),
        ]);

        $order = $transaction->order;
        Mail::to('contact@calatrava.fr')->send(new OrderPlacedMathilde($order));
        Mail::to($order->shippingAddress->contact_email)->send(new OrderPlaced($order));
    }

    public static function handleUnsuccessful(Transaction $transaction, WebhooksEvent $event): void
    {
        $transaction->fill([
            'success' => false,
            'type' => 'capture',
            'driver' => 'cawl',
            'amount' => $event->getPayment()->getPaymentOutput()->getAmountOfMoney()->getAmount(),
            'status' => 'Paiement échoué',
            'notes' => $event->getPayment()->getStatusOutput()->getErrors()[0]?->getMessage() ?? '',
            'card_type' => '',
            'last_four' => '',
            'captured_at' => now(),
        ]);
        $transaction->save();

        $order = $transaction->order;
        Mail::to($order->shippingAddress->contact_email)->send(new PaymentFailed($order));
    }

    public static function handleWebhook(WebhooksEvent $event): void
    {
        Cache::lock('write-transactions', 10)->get(function () use ($event) {
            $paymentId = $event->getPayment()->getId();
            $merchantReference = $event->getPayment()->getPaymentOutput()->getReferences()->getMerchantReference();
            $context = ['paymentId' => $paymentId, 'merchantReference' => $merchantReference, 'type' => $event->type, 'id' => $event->id];

            if (!collect(['payment.cancelled', 'payment.rejected', 'payment.pending_capture'])->contains($event->type)) {
                Log::debug('Ignoring event.', $context);
                return;
            }

            Log::debug("Handling webhook.", $context);

            $transaction = Transaction::where('reference', $paymentId)->first();

            if (!$transaction?->exists) {
                Log::debug("Transaction does not exist.", $context);
                $orderReference = $merchantReference;
                $order = Order::where('reference', $orderReference)->first();

                if (!$order?->exists) {
                    Log::error("Could not find webhook order", $context);
                    abort(404);
                }

                Log::debug("Found webhook order.", $context);

                $transaction = Transaction::make([
                    'order_id' => $order->id,
                    'reference' => $paymentId,
                ]);
            }

            if ($event->type === 'payment.pending_capture') {
                Log::debug('Handling PENDING_CAPTURE event.', $context);
                self::handlePendingCapture($transaction, $event);
            } else if (collect(['payment.cancelled', 'payment.rejected'])->contains($event->type)) {
                Log::debug('Handling unsuccessful payment event.', $context);
                self::handleUnsuccessful($transaction, $event);
            }
        });
    }
}
