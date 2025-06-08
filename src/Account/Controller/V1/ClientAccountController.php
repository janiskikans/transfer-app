<?php

declare(strict_types=1);

namespace App\Account\Controller\V1;

use App\Account\Dto\AccountDto;
use App\Account\Entity\Account;
use App\Account\Factory\AccountDtoFactory;
use App\Account\Repository\AccountRepositoryInterface;
use App\Client\Repository\ClientRepositoryInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Throwable;

class ClientAccountController extends AbstractController
{
    #[Route(
        path: '/client/{clientId}/accounts',
        name: 'v1_get_client_accounts',
        requirements: ['clientId' => Requirement::UUID],
        methods: ['GET'])
    ]
    #[OA\Parameter(
        name: 'clientId',
        description: 'Client ID',
        in: 'path',
        schema: new OA\Schema(type: 'string', format: 'uuid'),
    )]
    #[OA\Response(
        response: 200,
        description: 'Returns client accounts',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: AccountDto::class))
        )
    )]
    public function getClientAccounts(
        string $clientId,
        ClientRepositoryInterface $clientRepository,
        AccountRepositoryInterface $accountRepository,
        LoggerInterface $logger,
        AccountDtoFactory $accountDtoFactory,
    ): JsonResponse {
        $client = $clientRepository->getById($clientId);
        if (!$client) {
            throw $this->createNotFoundException('Client not found.');
        }

        try {
            $accounts = $accountRepository->getByClientId($clientId);

            $accountsDto = array_map(
                fn(Account $account) => $accountDtoFactory->createFromEntity($account),
                $accounts,
            );

            return $this->json($accountsDto);
        } catch (Throwable $e) {
            $logger->error($e);

            throw new RuntimeException('Failed to get accounts.');
        }
    }
}
