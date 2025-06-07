<?php

declare(strict_types=1);

namespace App\Transaction\Controller\V1;

use App\Transaction\Dto\TransferPostRequestDto;
use App\Transaction\Factory\TransferRequestDtoFactory;
use App\Transaction\Service\FundTransferService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class TransferController
{
    #[Route('/transfer', name: 'v1_transfer', methods: ['POST'])]
    public function transfer(
        #[MapRequestPayload] TransferPostRequestDto $transferPostRequest,
        TransferRequestDtoFactory  $transferRequestDtoFactory,
        FundTransferService $transferService
    ): JsonResponse {
        $transferRequest = $transferRequestDtoFactory->fromTransferPostRequest($transferPostRequest);

        $transferService->transfer($transferRequest);

        return new JsonResponse(['status' => 'success'], 200);
    }
}
