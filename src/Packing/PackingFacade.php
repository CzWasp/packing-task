<?php

declare(strict_types=1);

namespace App\Packing;

use App\Entity\Packaging;
use App\Entity\PackagingCache;
use App\Packing\Dto\BestBox;
use App\Packing\Dto\BinList;
use App\Packing\Dto\PackingData;
use App\Packing\Dto\ProductList;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class PackingFacade
{
    /**
     * @var \App\Packing\PackingService
     */
    private PackingService $packingService;

    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
        $this->packingService = new PackingService(
            new PackingApiCalculator(
                new PackingApiClient()
            ),
            new FallbackPackingCalculator(),
            new BestBinCacheService(),
        );
    }

    public function packIntoBestBox(string $jsonContent): BestBox
    {
        $productsData = json_decode($jsonContent, true);
        $packagingData = $this->getAvailablePackaging();
        $packingData = $this->createPackingData(
            $productsData,
            $packagingData
        );

        $bestBoxResult = $this->packingService->packIntoBestBox(
            $packingData,
            function (string $cacheKey): ?PackagingCache {
                return $this
                    ->entityManager
                    ->createQueryBuilder()
                    ->select('packagingCache')
                    ->from(PackagingCache::class, 'packagingCache')
                    ->where('packagingCache.cacheKey = :cacheKey')->setParameter('cacheKey', $cacheKey)
                    ->getQuery()
                    ->getOneOrNullResult();
            }
        );


        if (!$bestBoxResult->isLoadedFromCache()) {
            $packagingCache = new PackagingCache(
                $bestBoxResult->getCacheKey(),
                $this->getPackagingById($packagingData, $bestBoxResult->getBestBox()->getId())
            );
            $this->entityManager->persist($packagingCache);
            $this->entityManager->flush();
        }

        return $bestBoxResult->getBestBox();
    }

    /**
     * @return Packaging[]
     */
    public function getAvailablePackaging(): array
    {
        $query = $this
            ->entityManager
            ->createQueryBuilder()
            ->select('packaging')
            ->from(Packaging::class, 'packaging')
            ->getQuery();

        $query->enableResultCache(
            lifetime: 3600,
            resultCacheId: 'packaging_cache'
        );

        return $query->getResult();
    }

    /**
     * @param mixed[] $productsData
     * @param Packaging[] $packagingData
     * @return PackingData
     */
    private function createPackingData(
        array $productsData,
        array $packagingData
    ) {
        return new PackingData(
            ProductList::fromJson($productsData),
            BinList::fromPackagingList($packagingData),
        );
    }


    /**
     * @param Packaging[] $packagingList
     * @param int $id
     */
    private function getPackagingById(array $packagingList, int $id): Packaging
    {

        $matches = array_filter($packagingList, fn(Packaging $packaging) => $packaging->getId() === $id);
        if (count($matches) === 1) {
            return array_values($matches)[0];
        }

        throw new Exception('Packaging not found');
    }
}
