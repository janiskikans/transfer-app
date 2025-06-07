<?php

declare(strict_types=1);

namespace App\Transaction\Controller\V1;

use App\Account\Repository\AccountRepositoryInterface;
use App\Transaction\Entity\Transaction;
use App\Transaction\Factory\TransactionDtoFactory;
use App\Transaction\Repository\TransactionRepositoryInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Serializer\SerializerInterface;
use Throwable;

class AccountTransactionController extends AbstractController
{
    #[Route(
        path: '/account/{accountId}/transactions',
        name: 'v1_get_account_transactions',
        requirements: ['accountId' => Requirement::UUID],
        methods: ['GET']
    )]
    public function getAccountTransactions(
        string $accountId,
        Request $request,
        AccountRepositoryInterface $accountRepository,
        TransactionRepositoryInterface $transactionRepository,
        TransactionDtoFactory $transactionDtoFactory,
        SerializerInterface $serializer,
        LoggerInterface $logger
    ): JsonResponse {
        $account = $accountRepository->getById($accountId);
        if (!$account) {
            throw $this->createNotFoundException('Account not found.');
        }

        $transactions = $transactionRepository->getByAccountId(
            $accountId,
            max(0, $request->query->getInt('offset')),
            min(500, max(1, $request->query->getInt('limit', 20))),
        );

        try {
            $transactions = array_map(
                fn(Transaction $transaction) => $transactionDtoFactory->createFromEntity($transaction, $account),
                $transactions
            );

            $json = $serializer->serialize(
                [
                    'count' => count($transactions),
                    'data' => $transactions,
                ],
                'json'
            );
        } catch (Throwable $e) {
            $logger->error($e);

            throw new RuntimeException('Failed to get transactions.');
        }

        return JsonResponse::fromJsonString($json);
    }
}
