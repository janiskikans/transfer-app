<?php

declare(strict_types=1);

namespace App\Transaction\Controller\V1;

use App\Account\Repository\AccountRepositoryInterface;
use App\Transaction\Dto\TransactionDto;
use App\Transaction\Entity\Transaction;
use App\Transaction\Factory\TransactionDtoFactory;
use App\Transaction\Repository\TransactionRepositoryInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Throwable;

class AccountTransactionController extends AbstractController
{
    #[Route(
        path: '/account/{accountId}/transactions',
        name: 'v1_get_account_transactions',
        requirements: ['accountId' => Requirement::UUID],
        methods: ['GET']
    )]
    #[OA\Parameter(
        name: 'accountId',
        description: 'Account ID',
        in: 'path',
        schema: new OA\Schema(type: 'string', format: 'uuid'),
    )]
    #[OA\Parameter(
        name: 'offset',
        description: 'Offset',
        in: 'query',
        schema: new OA\Schema(type: 'integer'),
    )]
    #[OA\Parameter(
        name: 'limit',
        description: 'Limit',
        in: 'query',
        schema: new OA\Schema(type: 'integer'),
    )]
    #[OA\Response(
        response: 200,
        description: 'Returns account transactions',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: TransactionDto::class))
        )
    )]
    public function getAccountTransactions(
        string $accountId,
        Request $request,
        AccountRepositoryInterface $accountRepository,
        TransactionRepositoryInterface $transactionRepository,
        TransactionDtoFactory $transactionDtoFactory,
        LoggerInterface $logger,
    ): JsonResponse {
        $account = $accountRepository->getById($accountId);
        if (!$account) {
            throw $this->createNotFoundException('Account not found.');
        }

        try {
            $transactions = $transactionRepository->getByAccountId(
                $accountId,
                max(0, $request->query->getInt('offset')),
                min(500, max(1, $request->query->getInt('limit', 20))),
            );

            $transactionsDto = array_map(
                fn(Transaction $transaction) => $transactionDtoFactory->createFromEntity($transaction, $account),
                $transactions
            );

            return $this->json($transactionsDto);
        } catch (Throwable $e) {
            $logger->error($e);

            throw new RuntimeException('Failed to get transactions.');
        }
    }
}
