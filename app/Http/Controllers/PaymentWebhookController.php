<?php

namespace App\Http\Controllers;

use App\Actions\CawlActions;
use OnlinePayments\Sdk\Communication\ConnectionResponse;
use OnlinePayments\Sdk\Communication\ResponseClassMap;
use OnlinePayments\Sdk\Communication\ResponseFactory;
use OnlinePayments\Sdk\Domain\WebhooksEvent;

class PaymentWebhookController extends Controller
{
    public function __invoke() {
        $keyid = request()->header('x-gcs-keyid');
        $signature = request()->header('x-gcs-signature');
        $body = request()->getContent();

        $valid = CawlActions::verifyWebhookSignature($keyid, $signature, $body);
        if (!$valid) {
            abort(400);
        }

        $responseFactory = new ResponseFactory();
        $response = new ConnectionResponse(200, array('Content-Type' => 'application/json'), $body);
        $responseClassMap = new ResponseClassMap();
        $responseClassMap->addResponseClassName(200, WebhooksEvent::class);

        /** @var WebhooksEvent $event */
        $event = $responseFactory->createResponse($response, $responseClassMap);
        CawlActions::handleWebhook($event);

        return response('OK');
    }
}
