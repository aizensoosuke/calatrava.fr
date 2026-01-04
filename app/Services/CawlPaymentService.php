<?php

namespace App\Services;

use Closure;
use Exception;
use http\QueryString;
use OnlinePayments\Sdk\Authentication\V1HmacAuthenticator;
use OnlinePayments\Sdk\Client;
use OnlinePayments\Sdk\Communicator;
use OnlinePayments\Sdk\CommunicatorConfiguration;
use OnlinePayments\Sdk\Domain\AmountOfMoney;
use OnlinePayments\Sdk\Domain\CreateHostedCheckoutRequest;
use OnlinePayments\Sdk\Domain\CreateHostedCheckoutResponse;
use OnlinePayments\Sdk\Domain\HostedCheckoutSpecificInput;
use OnlinePayments\Sdk\Domain\Order;
use OnlinePayments\Sdk\ValidationException;

class CawlPaymentService
{
    private Client $client;
    private AmountOfMoney $amountOfMoney;
    private Order $order;

    public function __construct()
    {
        $communicatorConfiguration = new CommunicatorConfiguration(
            config('cawl.api_key'),
            config('cawl.api_secret'),
            config('cawl.api_endpoint'),
            config('cawl.integrator'),
            null
        );

        $authenticator = new V1HmacAuthenticator($communicatorConfiguration);
        $communicator = new Communicator($communicatorConfiguration, $authenticator);

        $this->client = new Client($communicator);
    }

    public static function make(): self {
        return new self();
    }

    public function configureOrder(Closure $orderConfigurator): self
    {
        $this->order = new Order();
        $orderConfigurator($this->order);
        return $this;
    }

    public function finalize(): CreateHostedCheckoutResponse
    {
        $createHostedCheckoutRequest = new CreateHostedCheckoutRequest();

        $hostedCheckoutSpecificInput = new HostedCheckoutSpecificInput();
        $hostedCheckoutSpecificInput->setReturnUrl(route('payment-callback', absolute: true));
        $hostedCheckoutSpecificInput->setLocale('fr_FR');

        $createHostedCheckoutRequest->setOrder($this->order);
        $createHostedCheckoutRequest->setHostedCheckoutSpecificInput($hostedCheckoutSpecificInput);

        try {
            return $this->client
                ->merchant(config('cawl.psp_id'))
                ->hostedCheckout()
                ->createHostedCheckout($createHostedCheckoutRequest);
        } catch (ValidationException $e) {
            $message = 'Erreur de validation des entrées:' . PHP_EOL;
            foreach ($e->getErrors() as $error) {
                $message .= '- ';
                if (!empty($error->getPropertyName())) {
                    $message .= $error->getPropertyName() . ': ';
                }

                $message .= $error->getMessage();
                $message .= '(' . $error->getCode() . ')' . PHP_EOL;
            }

            throw new \Exception($message);
        }
    }

    public function verifyCallback() {
        $hostedCheckoutId = request()->query('hostedCheckoutId');

        $hostedCheckout = $this->client->merchant(config('cawl.psp_id'))
            ->hostedCheckout()
            ->getHostedCheckout($hostedCheckoutId);

        dd($hostedCheckout->getStatus());
    }
}
