<?php

declare(strict_types=1);

namespace App\Currency\Command;

use App\Currency\Service\CurrencyRateImportService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'currency:import-rates',
    description: 'Import currency rates from external source',
)]
class CurrencyRateImportCommand extends Command
{
    public function __construct(private readonly CurrencyRateImportService $rateImportService, private string $exchangeRateHostAccessKey)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->info('Importing currency rates...');

        $this->rateImportService->importAndSaveRates();

        $io->success('Currency rates imported successfully!');;

        return Command::SUCCESS;
    }
}
