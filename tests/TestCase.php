<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto;

use ArkEcosystem\Crypto\Configuration\Network;
use ArkEcosystem\Crypto\Networks\Testnet;
use ArkEcosystem\Crypto\Transactions\Deserializer;
use ArkEcosystem\Crypto\Transactions\Types\AbstractTransaction;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use Concerns\Fixtures;
    use Concerns\Serialize;
    use Concerns\Deserialize;

    protected $address = '0x6f0182a0cc707b055322ccf6d4cb6a5aff1aeb22';

    protected $passphrase = 'found lobster oblige describe ready addict body brave live vacuum display salute lizard combine gift resemble race senior quality reunion proud tell adjust angle';

    protected $secondPassphrase = 'gold favorite math anchor detect march purpose such sausage crucial reform novel connect misery update episode invite salute barely garbage exclude winner visa cruise';

    protected $passphrases = [
        'album pony urban cheap small blade cannon silent run reveal luxury glad predict excess fire beauty hollow reward solar egg exclude leaf sight degree',
        'hen slogan retire boss upset blame rocket slender area arch broom bring elder few milk bounce execute page evoke once inmate pear marine deliver',
        'top visa use bacon sun infant shrimp eye bridge fantasy chair sadness stable simple salad canoe raw hill target connect avoid promote spider category',
    ];

    protected function setUp(): void
    {
        Network::set(Testnet::new());
    }

    protected function assertTransaction(array $fixture): AbstractTransaction
    {
        $actual = $this->assertDeserialized($fixture, [
            'hash',
            'nonce',
            'value',
            'gasPrice',
            'gasLimit',
            'contractId',
            'senderPublicKey',
            'from',
            'to',
            'v',
            'r',
            's',
        ]);

        $this->assertTrue($actual->verify());

        return $actual;
    }

    protected function getTransaction($file = 'transfer'): AbstractTransaction
    {
        $fixture = $this->getTransactionFixture('evm_call', $file);

        return Deserializer::new($fixture['serialized'])->deserialize();
    }
}
