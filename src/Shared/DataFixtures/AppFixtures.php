<?php

declare(strict_types=1);

namespace App\Shared\DataFixtures;

use App\Account\Factory\AccountFactory;
use App\Currency\Enum\Currency;
use App\Currency\Factory\CurrencyFactory;
use App\Client\Factory\ClientFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    private const array CURRENCIES = [
        Currency::EUR,
        Currency::USD,
        Currency::GBP,
    ];

    public function load(ObjectManager $manager): void
    {
        $currencies = [];

        foreach (self::CURRENCIES as $currency) {
            $currencies[] = CurrencyFactory::createOne([
                'code' => $currency->value,
                'name' => $currency->value,
                'decimalPlaces' => 2,
            ]);
        }

        $clients = ClientFactory::createMany(5);

        foreach ($clients as $client) {
            foreach ($currencies as $currency) {
                $account = AccountFactory::createOne([
                    'client' => $client,
                    'currency' => $currency,
                ]);

                $client->addAccount($account);
            }
        }
    }
}
