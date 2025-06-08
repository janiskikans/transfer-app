<?php

declare(strict_types=1);

namespace App\Account\Controller\V1;

use App\Account\Entity\Account;
use App\Account\Factory\AccountDtoFactory;
use App\Account\Repository\AccountRepositoryInterface;
use App\Client\Repository\ClientRepositoryInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Serializer\SerializerInterface;
use Throwable;

class ClientAccountController extends AbstractController
{
    #[Route(
        path: '/client/{clientId}/accounts',
        name: 'v1_get_client_accounts',
        requirements: ['clientId' => Requirement::UUID],
        methods: ['GET'])
    ]
    public function getClientAccounts(
        string $clientId,
        ClientRepositoryInterface $clientRepository,
        AccountRepositoryInterface $accountRepository,
        SerializerInterface $serializer,
        LoggerInterface $logger,
        AccountDtoFactory $accountDtoFactory,
    ): JsonResponse {
        $client = $clientRepository->getById($clientId);
        if (!$client) {
            throw $this->createNotFoundException('Client not found.');
        }

        $accounts = $accountRepository->getByClientId($clientId);

        try {
            $accountsDto = array_map(
                fn(Account $account) => $accountDtoFactory->createFromEntity($account),
                $accounts,
            );

            $json = $serializer->serialize(
                [
                    'count' => count($accountsDto),
                    'data' => $accountsDto,
                ],
                'json'
            );
        } catch (Throwable $e) {
            $logger->error($e);

            throw new RuntimeException('Failed to get accounts.');
        }

        return JsonResponse::fromJsonString($json);
    }
}
