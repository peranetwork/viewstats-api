<?php

require_once __DIR__ . '/../vendor/autoload.php';

use ViewStatsWrapper\ViewStatsAPI;

$apiToken = '32ev9m0qggn227ng1rgpbv5j8qllas8uleujji3499g9had6oj7f0ltnvrgi00cq';
$api = new ViewStatsAPI($apiToken);

try {
    $getResult = $api->get('/channels/popular', ['total' => 1]);
    print_r($getResult);
} catch (\ViewStatsWrapper\Exceptions\APIException $e) {
    echo "API Error: " . $e->getMessage();
} catch (\ViewStatsWrapper\Exceptions\DecryptionException $e) {
    echo "Decryption Error: " . $e->getMessage();
}