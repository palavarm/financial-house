<?php

require 'vendor/autoload.php';

use GuzzleHttp\Client;

$baseUrl = 'https://sandbox-reporting.rpdpymnt.com';

$email = 'demo@financialhouse.io';
$password = 'cjaiU8CV';

$client = new Client([
    'base_uri' => $baseUrl,
    'headers' => [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ]
]);

// Step 1: Authenticate
$authResponse = $client->post('/api/v3/merchant/user/login', [
    'json' => [
        'email' => $email,
        'password' => $password,
    ]
]);

$authData = json_decode($authResponse->getBody(), true);

if (!isset($authData['token'])) {
    echo "Authentication failed.\n";
    exit(1);
}

$token = $authData['token'];

echo "Authenticated successfully.\n";

// Step 2: Send Report Request
$reportParams = [
    "transactionId" => "1-1444392550-1"
];

echo $token;

try {
    $reportResponse = $client->post('/api/v3/client', [
        'headers' => [
            'Authorization' => 'Bearer ' . $token,
        ],
        'json' => $reportParams,
    ]);

    $reportData = json_decode($reportResponse->getBody(), true);

    echo "Report Response:\n";
    print_r($reportData);
} catch (\Exception $e) {
    echo "Error calling report endpoint:\n";
    echo $e->getMessage() . "\n";

    if (method_exists($e, 'getResponse') && $e->getResponse()) {
        $errorBody = $e->getResponse()->getBody()->getContents();
        echo "Response Body:\n$errorBody\n";
    }
}
