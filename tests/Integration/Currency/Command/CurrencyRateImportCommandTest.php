<?php

declare(strict_types=1);

namespace App\Tests\Integration\Currency\Command;

use App\Tests\DummyFactory\Currency\CurrencyFactory;
use App\Tests\Integration\HasEntityManager;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class CurrencyRateImportCommandTest extends KernelTestCase
{
    use HasEntityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setupEntityManager();
    }

    public function testExecute(): void
    {
        $this->prepareApp();

        $application = new Application(self::$kernel);

        $command = $application->find('currency:import-rates');
        $commandTester = new CommandTester($command);

        $commandTester->execute([]);
        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();
        self::assertStringContainsString('Import successful', $output);
    }

    private function prepareApp(): void
    {
        self::bootKernel();

        $usd = CurrencyFactory::create();
        $this->entityManager->persist($usd);

        $eur = CurrencyFactory::create('EUR', 'Euro');
        $this->entityManager->persist($eur);

        $gbp = CurrencyFactory::create('GBP', 'British Pound');
        $this->entityManager->persist($gbp);

        $this->entityManager->flush();
    }
}
