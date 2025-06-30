<?php

namespace App;

use App\Packing\PackingFacade;
use Doctrine\ORM\EntityManager;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Application
{
    private EntityManager $entityManager;

    public function __construct(
        EntityManager $entityManager,
    ) {
        $this->entityManager = $entityManager;
    }

    public function run(RequestInterface $request): ResponseInterface
    {
        try {
            $packingFacade = new PackingFacade($this->entityManager);
            $bestBox = $packingFacade->packIntoBestBox($request->getBody()->getContents());
            $body = json_encode(['id' => $bestBox->getId()]);
            if ($body) {
                return new Response(
                    status: 200,
                    body: $body
                );
            }

            return new Response(
                status: 500,
                body: 'Internal server error'
            );
        } catch (\Throwable $e) {
            return new Response(
                status: 500,
                body: 'Internal server error' . $e->getMessage()
            );
        }
    }
}
