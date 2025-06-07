<?php

declare(strict_types=1);

namespace App\Account\Controller\V1;

use App\Account\Repositories\AccountRepositoryInterface;
use App\Client\Repositories\ClientRepositoryInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Serializer\SerializerInterface;
use Throwable;

class AccountController extends AbstractController
{
    #[Route(
        path: '/client/{clientId}/accounts',
        name: 'v1_account_list_by_client',
        requirements: ['clientId' => Requirement::UUID],
        methods: ['GET'])
    ]
    public function listClientAccounts(
        string $clientId,
        ClientRepositoryInterface $clientRepository,
        AccountRepositoryInterface $accountRepository,
        SerializerInterface $serializer,
        LoggerInterface $logger
    ): JsonResponse {
        $client = $clientRepository->getById($clientId);
        if (!$client) {
            throw $this->createNotFoundException('Client not found.');
        }

        $accounts = $accountRepository->getByClientId($clientId);

        try {
            $json = $serializer->serialize($accounts, 'json', ['groups' => 'api']);
        } catch (Throwable $e) {
            $logger->error($e);

            throw new RuntimeException('Failed to get accounts.');
        }

        return JsonResponse::fromJsonString($json);
    }
}
