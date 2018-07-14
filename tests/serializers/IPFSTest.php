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

namespace ArkEcosystem\Tests\Crypto\Serializers;

use ArkEcosystem\Crypto\Serializer;
use ArkEcosystem\Crypto\Serializers\IPFS;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * This is the ipfs serializer test class.
 *
 * @author Brian Faust <brian@ark.io>
 * @coversNothing
 */
class IPFSTest extends TestCase
{
    /** @test */
    public function it_should_serialize_the_transaction_with_a_passphrase()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');

        $this->assertSerialized($this->getTransactionFixture(5, 'passphrase'));
    }

    /** @test */
    public function it_should_serialize_the_transaction_with_a_second_passphrase()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');

        $this->assertSerialized($this->getTransactionFixture(5, 'second-passphrase'));
    }
}
