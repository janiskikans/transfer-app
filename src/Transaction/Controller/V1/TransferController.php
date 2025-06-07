<?php

declare(strict_types=1);

namespace App\Transaction\Controller\V1;

use App\Transaction\Dto\TransferRequestDto;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class TransferController
{
    #[Route('/transfer', name: 'v1_transfer', methods: ['POST'])]
    public function transfer(
        #[MapRequestPayload] TransferRequestDto $transferData
    ): JsonResponse {
        // TODO

        return new JsonResponse(['status' => 'success'], 200);
    }
}
