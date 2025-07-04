<?php

declare(strict_types=1);

namespace App\Packing\Dto;

class BestBox
{
    public function __construct(
        private readonly int $id
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }
}
