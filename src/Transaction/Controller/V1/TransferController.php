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
use OpenApi\Attributes as OA;

class TransferController
{
    #[Route('/transfer', name: 'v1_transfer', methods: ['POST'])]
    #[OA\Response(
        response: 200,
        description: 'Returns transfer status',
        content: new OA\JsonContent(
            type: 'object',
            example: [
                'status' => 'success',
                'message' => 'Fund transfer successful',
            ]
        )
    )]
    #[OA\Response(
        response: 500,
        description: 'Returns transfer status',
        content: new OA\JsonContent(
            type: 'object',
            example: [
                'status' => 'failed',
                'message' => 'Fund transfer failed',
            ]
        )
    )]
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

            return new JsonResponse(['status' => 'failed', 'message' => 'Fund transfer failed']);
        }

        return new JsonResponse(['status' => 'success', 'message' => 'Fund transfer successful'], 200);
    }
}
