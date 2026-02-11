<?php

return [
    // 'testing' or 'production'
    'mode' => env('SUNAT_MODE', 'testing'),
    'ruc' => env('SUNAT_RUC', ''),
    'username' => env('SUNAT_USER', ''),
    'password' => env('SUNAT_PASS', ''),
    'certificate_path' => env('SUNAT_CERT_PATH', ''), // ruta al .pfx/.pem si aplica
    'certificate_passphrase' => env('SUNAT_CERT_PASS', ''),
    'currency' => env('SUNAT_CURRENCY', 'PEN'),
    'endpoints' => [
        'testing' => env('SUNAT_ENDPOINT_TEST', ''),
        'production' => env('SUNAT_ENDPOINT_PROD', ''),
    ],
];
