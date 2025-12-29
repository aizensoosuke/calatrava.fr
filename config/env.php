<?php

return [
    'shipping' => [
        'fees' => env('ENV_SHIPPING_FEES', 500),
        'free_from' => env('ENV_SHIPPING_FREE_FROM', 6000),
    ],

    'card_payment_descriptor' => env("ENV_CARD_PAYMENT_DESCRIPTOR", "CALATRAVA"),
];
