<?php

namespace App\Packing;

use App\Packing\Dto\BestBox;
use App\Packing\Dto\PackingData;

interface PackingInterface
{
    public function getBestBoxForPackingData(PackingData $packingData): BestBox;
}
