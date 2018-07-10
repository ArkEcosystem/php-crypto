<?php

declare(strict_types=1);

/*
 * This file is part of Ark PHP Crypto.
 *
 * (c) Ark Ecosystem <info@ark.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ArkEcosystem\Tests\Crypto\Identity;

use ArkEcosystem\Crypto\Identity\PublicKey as TestClass;
use ArkEcosystem\Tests\Crypto\TestCase;
use BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PublicKey as EcPublicKey;

/**
 * This is the address test class.
 *
 * @author Brian Faust <brian@ark.io>
 * @coversNothing
 */
class PublicKeyTest extends TestCase
{
    /** @test */
    public function it_should_get_the_public_key_from_secret()
    {
        $actual = TestClass::fromSecret('this is a top secret passphrase');

        $this->assertInstanceOf(EcPublicKey::class, $actual);
        $this->assertInternalType('string', $actual->getHex());
        $this->assertSame('034151a3ec46b5670a682b0a63394f863587d1bc97483b1b6c70eb58e7f0aed192', $actual->getHex());
    }

    /** @test */
    public function it_should_get_the_public_key_from_hex()
    {
        $actual = TestClass::fromHex('034151a3ec46b5670a682b0a63394f863587d1bc97483b1b6c70eb58e7f0aed192');

        $this->assertSame('034151a3ec46b5670a682b0a63394f863587d1bc97483b1b6c70eb58e7f0aed192', $actual->getHex());
    }

    /** @test */
    public function it_should_validate_the_public_key()
    {
        $actual = TestClass::validate('034151a3ec46b5670a682b0a63394f863587d1bc97483b1b6c70eb58e7f0aed192');

        $this->assertTrue($actual);
    }
}
