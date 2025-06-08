<?php

declare(strict_types=1);

namespace App\Client\Controller\V1;

use App\Client\Entity\Client;
use App\Client\Factory\ClientDtoFactory;
use App\Client\Repository\ClientRepositoryInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Throwable;

class ClientController extends AbstractController
{
    #[Route(path: '/client/all', name: 'v1_get_all_clients', methods: ['GET'])]
    public function getAll(
        ClientRepositoryInterface $clientRepository,
        ClientDtoFactory $clientDtoFactory,
        SerializerInterface $serializer,
        LoggerInterface $logger,
    ): JsonResponse {
        $clients = $clientRepository->getAll();

        try {
            $clientsDto = array_map(fn(Client $client) => $clientDtoFactory->createFromEntity($client), $clients);

            $json = $serializer->serialize(
                [
                    'count' => count($clientsDto),
                    'data' => $clientsDto,
                ],
                'json'
            );
        } catch (Throwable $e) {
            $logger->error($e);

            throw new RuntimeException('Failed to get clients.');
        }

        return JsonResponse::fromJsonString($json);
    }
}
