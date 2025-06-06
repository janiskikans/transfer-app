<?php

declare(strict_types=1);

namespace App\Account\Controller\V1;

use App\Account\Repositories\AccountRepositoryInterface;
use App\Client\Repositories\ClientRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

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
        AccountRepositoryInterface $accountRepository
    ): JsonResponse {
        $client = $clientRepository->getById($clientId);
        if (!$client) {
            throw $this->createNotFoundException('Client not found.');
        }

        $accounts = $accountRepository->getByClientId($clientId);

        return $this->json($accounts);
    }
}
