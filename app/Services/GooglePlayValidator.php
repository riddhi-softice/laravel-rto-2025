<?php

namespace App\Services;

use Google\Client;
use Google\Service\AndroidPublisher;

class GooglePlayValidator
{
    protected $client;
    protected $androidPublisherService;

    // public function __construct()
    // {
    //     $this->client = new Client();
    //     // $this->client->setAuthConfig(base_path(env('GOOGLE_APPLICATION_CREDENTIALS')));
    //     $client->setAuthConfig(storage_path('app/service-account-file.json'));
    //     // $this->client->addScope(AndroidPublisher::ANDROIDPUBLISHER);

    //     $this->androidPublisherService = new AndroidPublisher($this->client);
    // }

    public function validatePurchase($packageName, $productId, $purchaseToken)
    {
        // try {
            $productPurchase = $this->androidPublisherService->purchases_products->get(
                $packageName,
                $productId,
                $purchaseToken
            );
            dd($productPurchase);

            if ($productPurchase->getPurchaseState() == 0) {
                // Purchase is valid
                return [
                    'valid' => true,
                    'message' => 'Purchase is valid',
                    'data' => $productPurchase
                ];
            }

            return [
                'valid' => false,
                'message' => 'Purchase is not valid',
            ];

        // } catch (GoogleServiceException $e) {
        //     return [
        //         'valid' => false,
        //         'message' => 'Error validating purchase: ' . $e->getMessage(),
        //     ];
        // }
    }
}
