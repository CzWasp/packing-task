<?php

declare(strict_types=1);

namespace App\Packing;

use App\Packing\Dto\PackingData;
use App\Packing\Dto\PackingRequest;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use RuntimeException;

class PackingApiClient
{
    private const string PACKING_API_ENDPOINT = 'https://eu.api.3dbinpacking.com/';
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => self::PACKING_API_ENDPOINT,
            'timeout' => 10.0,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function fetchPackingResult(PackingData $packingData): array
    {
        $request = new PackingRequest($packingData);

        try {
            $response = $this->client->post('packer/packIntoMany', [
                'form_params' => [
                    'query' => json_encode($request->toArray())
                ]
            ]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            $message = $e->hasResponse() && $e->getResponse() !== null
                ? $e->getResponse()->getBody()->getContents()
                : $e->getMessage();

            throw new RuntimeException("API request failed: " . $message);
        }
    }
}
