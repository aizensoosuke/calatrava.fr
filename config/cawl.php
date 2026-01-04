<?php

return [
    'psp_id' => env('CAWL_PSPID', null),
    'api_key' => env('CAWL_API_KEY', null),
    'api_secret' => env('CAWL_API_SECRET', null),
    'api_endpoint' => env('CAWL_API_ENDPOINT', 'https://payment.preprod.cawl-solutions.fr'),
    'integrator' => env('CAWL_INTEGRATOR', 'Calatrava'),
    'return_url' => env('CAWL_RETURN_URL', 'https://calatrava.fr/payment-callback'),
];
