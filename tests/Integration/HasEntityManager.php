<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use Doctrine\ORM\EntityManagerInterface;

trait HasEntityManager
{
    private EntityManagerInterface $entityManager;

    private function setupEntityManager(): void
    {
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        assert($entityManager instanceof EntityManagerInterface);
        $this->entityManager = $entityManager;
    }
}
