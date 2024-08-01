<?php

declare(strict_types=1);



namespace ArkEcosystem\Tests\Crypto;

use ArkEcosystem\Crypto\Configuration\Network;
use ArkEcosystem\Crypto\Networks\Mainnet;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use Concerns\Fixtures;
    use Concerns\Serialize;
    use Concerns\Deserialize;

    protected $passphrase = 'my super secret passphrase';

    protected $secondPassphrase = 'this is a top secret second passphrase';

    protected $passphrases = [
        'album pony urban cheap small blade cannon silent run reveal luxury glad predict excess fire beauty hollow reward solar egg exclude leaf sight degree',
        'hen slogan retire boss upset blame rocket slender area arch broom bring elder few milk bounce execute page evoke once inmate pear marine deliver',
        'top visa use bacon sun infant shrimp eye bridge fantasy chair sadness stable simple salad canoe raw hill target connect avoid promote spider category',
    ];

    protected function setUp(): void
    {
        Network::set(Mainnet::new());
    }
}
