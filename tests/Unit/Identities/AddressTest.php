<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto\Unit\Identities;

use ArkEcosystem\Crypto\Configuration\Network;
use ArkEcosystem\Crypto\Identities\Address as TestClass;
use ArkEcosystem\Crypto\Identities\PrivateKey;
use ArkEcosystem\Crypto\Networks\Devnet;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * @covers \ArkEcosystem\Crypto\Identities\Address
 */
class AddressTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Network::set(Devnet::new());
    }

    /** @test */
    public function it_should_get_the_address_from_passphrase()
    {
        $fixture = $this->getFixture('identity');

        $actual = TestClass::fromPassphrase($fixture['passphrase']);

        $this->assertSame($fixture['data']['address'], $actual);
    }

    /** @test */
    public function it_should_get_the_address_from_a_multi_signature_asset()
    {
        $actual = TestClass::fromMultiSignatureAsset(3, [
            '02fb3def2593a00c5b84620addf28ff21bac452bd71a37d4d8e24f301683a81b56',
            '02bc9f661fcc8abca65fe9aff4614036867b7fdcc5730085ccc5cb854664d0194b',
            '03c44c6b6cc9893ae21ca606712fd0f6f03c41ce81c4f6ce5a640f4b0b82ec1ce0',
            '020300039e973baf5e46b945777cfae330d6392cdb039b1cebc5c3382d421166c3',
            '03b050073621b9b5caec9461d44d6bcf21a858c47dd88230ce723e25c1bc75c219',
        ]);

        $this->assertSame('0x8246206ef20b95D0a3C16704Ee971a605cb7E33E', $actual);
    }

    /** @test */
    public function it_should_get_the_address_from_public_key()
    {
        $fixture = $this->getFixture('identity');

        $actual = TestClass::fromPublicKey($fixture['data']['publicKey']);

        $this->assertSame($fixture['data']['address'], $actual);
    }

    /** @test */
    public function it_should_get_the_address_from_private_key()
    {
        $fixture = $this->getFixture('identity');

        $privateKey = PrivateKey::fromPassphrase($fixture['passphrase']);

        $actual = TestClass::fromPrivateKey($privateKey);

        $this->assertSame($fixture['data']['address'], $actual);
    }
}
