<?php

declare(strict_types=1);

namespace App\Currency\Command;

use App\Currency\Service\CurrencyRateImportService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

#[AsCommand(
    name: 'currency:import-rates',
    description: 'Import currency rates from external source',
)]
class CurrencyRateImportCommand extends Command
{
    public function __construct(
        private readonly CurrencyRateImportService $rateImportService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->info('Importing currency rates...');

        try {
            $result = $this->rateImportService->importAndSaveRates();
        } catch (Throwable $e) {
            $io->error('Failed to import currency rates: ' . $e->getMessage());

            return Command::FAILURE;
        }

        $io->success(
            sprintf(
                'Imported %d new and %d updated currency rates.',
                $result->getNewCount(),
                $result->getUpdatedCount()
            )
        );;

        return Command::SUCCESS;
    }
}
