<?php

declare(strict_types=1);

namespace App\Packing;

use App\Packing\Dto\BestBox;
use App\Packing\Dto\PackingData;
use RuntimeException;

class PackingApiCalculator implements PackingInterface
{
    public function __construct(
        private readonly PackingApiClient $client
    ) {
    }

    public function getBestBoxForPackingData(PackingData $packingData): BestBox
    {
        $response = $this->client->fetchPackingResult($packingData);
        if (!isset($response['response'])) {
            throw new RuntimeException("Missing 'response' in API result.");
        }

        $body = $response['response'];
        if (isset($body['status']) && $body['status'] !== 1) {
            if (!empty($body['errors'])) {
                throw PackingApiException::fromErrorArray($body['errors']);
            }
            throw new RuntimeException("API response indicates failure.");
        }

        if (empty($body['bins_packed'])) {
            throw new RuntimeException("No packed bins returned.");
        }

        if (count($body['bins_packed']) > 1) {
            throw new RuntimeException("Cannot pack into more than one bin.");
        }

        return new BestBox(
            (int) $body['bins_packed'][0]['bin_data']['id']
        );
    }
}
