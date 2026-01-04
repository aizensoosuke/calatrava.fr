<?php

namespace App\Actions;

use App\Data\CartLineData;
use App\Services\CawlPaymentService;
use Exception;
use Illuminate\Validation\UnauthorizedException;
use Lunar\Facades\CartSession;
use Lunar\Facades\ShippingManifest;
use Lunar\Models\CartLine;
use Lunar\Models\Country;
use OnlinePayments\Sdk\Domain\AmountOfMoney;
use OnlinePayments\Sdk\Domain\CreateHostedCheckoutResponse;
use OnlinePayments\Sdk\Domain\Customer;
use OnlinePayments\Sdk\Domain\LineItem;
use OnlinePayments\Sdk\Domain\Order;
use OnlinePayments\Sdk\Domain\OrderLineDetails;
use OnlinePayments\Sdk\Domain\PersonalInformation;
use OnlinePayments\Sdk\Domain\PersonalName;
use OnlinePayments\Sdk\Domain\Shipping;
use OnlinePayments\Sdk\Domain\ShippingMethod;
use OnlinePayments\Sdk\Domain\ShoppingCart;

class CheckoutAction
{
    public static function execute(): CreateHostedCheckoutResponse
    {
        $user = auth()->user();
        if ($user === null) {
            throw new UnauthorizedException;
        }

        $cart = CartSession::current();
        if ($cart === null) {
            throw new Exception('No cart available for checkout');
        }

        $items = $cart->lines->map(function (CartLine $line) {
            $lineData = CartLineData::fromModel($line);
            $images = $lineData->product
                ->thumbnails->take(8)
                ->map->url
                ->map(fn($url) => str($url)->replace('local', 'fr'))
                ->map(function ($url) {
                    // https://stackoverflow.com/a/27124836 (encode only non-ascii chars)
                    return preg_replace_callback('/[^\x20-\x7f]/', function ($match) {
                        return urlencode($match[0]);
                    }, $url);
                })
                ->toArray();

            return [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $lineData->product->name,
                        'images' => $images,
                    ],
                    'unit_amount' => $lineData->product->price * 100,
                ],
                'quantity' => $lineData->quantity,
            ];
        });

        $shippingTotal = 500;
        if ($cart->total->value > 10000) {
            $shippingTotal = 0;
        }

        $country = Country::firstWhere('iso2', 'FR');

        $cart->setShippingAddress([
            'first_name' => 'dummy',
            'line_one' => 'dummy',
            'city' => 'dummy',
            'postcode' => 'dummy',
            'country_id' => $country->id,
        ]);
        $cart->setBillingAddress($cart->shippingAddress);

        $cart->calculate();
        $shippingOptionId = $cart->subTotal->value > 10000 ? 'CHRONOPOST_FREE' : 'CHRONOPOST';

        $shippingOption = ShippingManifest::getOptions($cart)->firstWhere('identifier', $shippingOptionId);
        $cart->setShippingOption($shippingOption);

        $order = $cart->createOrder();
        $order->update([
            'placed_at' => now()
        ]);

        $grandTotal = $cart->total->value + $shippingTotal;

        return app(CawlPaymentService::class)
            ->configureOrder(function (Order $order) use ($grandTotal, $shippingTotal, $user, $cart) {
                $customerName = new PersonalName();
                $customerName->setFirstName($user->name);
                $customerPersonalInformation = new PersonalInformation();
                $customerPersonalInformation->setName($customerName);

                $customer = new Customer();
                $customer->setMerchantCustomerId($user->id);
                $customer->setPersonalInformation($customerPersonalInformation);

                $shippingMethod = new ShippingMethod();
                $shippingMethod->setName('Chronopost');
                $shippingMethod->setDetails('5 à 7 jours ouvrés');

                $shipping = new Shipping();
                $shipping->setEmailAddress($user->email);
                $shipping->setShippingCost($shippingTotal);
                $shipping->setType('2-day-or-more');
                $shipping->setMethod($shippingMethod);

                $shoppingCartItems = $cart->lines->map(function (CartLine $line) {
                    $lineAmountOfMoney = new AmountOfMoney();
                    $lineAmountOfMoney->setAmount($line->total->value);
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

                $order->setAmountOfMoney($amountOfMoney);
                $order->setShipping($shipping);
                $order->setCustomer($customer);
                $order->setShoppingCart($shoppingCart);
            })
            ->finalize();
    }
}
