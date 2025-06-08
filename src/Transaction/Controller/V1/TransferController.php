<?php

declare(strict_types=1);

namespace App\Transaction\Controller\V1;

use App\Transaction\Dto\TransferPostRequestDto;
use App\Transaction\Exception\InvalidTransferRequestException;
use App\Transaction\Exception\TransferFailedException;
use App\Transaction\Factory\TransferRequestDtoFactory;
use App\Transaction\Service\FundTransferService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

class TransferController
{
    #[Route('/transfer', name: 'v1_transfer', methods: ['POST'])]
    public function transfer(
        #[MapRequestPayload] TransferPostRequestDto $transferPostRequest,
        TransferRequestDtoFactory $transferRequestDtoFactory,
        FundTransferService $transferService,
        LoggerInterface $logger,
    ): JsonResponse {
        $transferRequest = $transferRequestDtoFactory->fromTransferPostRequest($transferPostRequest);

        try {
            $transferService->transfer($transferRequest);
        } catch (TransferFailedException | InvalidTransferRequestException $e) {
            return new JsonResponse(['status' => 'failed', 'message' => $e->getMessage()]);
        } catch (Throwable $e) {
            $logger->error($e);

            return new JsonResponse(['status' => 'failed', 'message' => 'Fund transfer failed.']);
        }

        return new JsonResponse(['status' => 'success', 'message' => 'Fund transfer successful'], 200);
    }
}
