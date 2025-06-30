<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class PackagingCache
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, unique: true)]
    private string $cacheKey;

    #[ORM\ManyToOne(targetEntity: Packaging::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Packaging $packaging;

    public function __construct(
        string $cacheKey,
        Packaging $packaging
    ) {
        $this->cacheKey = $cacheKey;
        $this->packaging = $packaging;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCacheKey(): string
    {
        return $this->cacheKey;
    }

    public function getPackaging(): Packaging
    {
        return $this->packaging;
    }
}
