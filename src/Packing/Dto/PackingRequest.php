<?php

declare(strict_types=1);

namespace App\Packing\Dto;

class PackingRequest
{
    private const string API_KEY = 'cf76ba9733ebc329cfd86a8f08cccb94';
    private const string USERNAME = 'iocfdhcwcpdfbmdkuz@xfavaj.com';
    public function __construct(
        private readonly PackingData $packingData
    ) {
    }

    /**
     * @return mixed[]
     */
    public function toArray(): array
    {
        return [
            'username' => self::USERNAME,
            'api_key' => self::API_KEY,
            'items' => $this->packingData->getProductList()->toArray(),
            'bins' => $this->packingData->getBinList()->toArray(),
            'params' => [],
        ];
    }
}
