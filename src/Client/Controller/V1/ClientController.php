<?php

declare(strict_types=1);

namespace App\Client\Controller\V1;

use App\Client\Dto\ClientDto;
use App\Client\Entity\Client;
use App\Client\Factory\ClientDtoFactory;
use App\Client\Repository\ClientRepositoryInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

class ClientController extends AbstractController
{
    #[Route(path: '/client/all', name: 'v1_get_all_clients', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns all clients',
        content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: new Model(type: ClientDto::class))),
    )]
    public function getAll(
        ClientRepositoryInterface $clientRepository,
        ClientDtoFactory $clientDtoFactory,
        LoggerInterface $logger,
    ): JsonResponse {
        try {
            $clients = $clientRepository->getAll();

            $clientsDto = array_map(fn(Client $client) => $clientDtoFactory->createFromEntity($client), $clients);

            return $this->json($clientsDto);
        } catch (Throwable $e) {
            $logger->error($e);

            throw new RuntimeException('Failed to get clients.');
        }
    }
}
