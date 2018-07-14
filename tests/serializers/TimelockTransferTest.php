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
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * This is the timelock transfer serializer test class.
 *
 * @author Brian Faust <brian@ark.io>
 * @coversNothing
 */
class TimelockTransferTest extends TestCase
{
    /** @test */
    public function it_should_serialize_the_transaction_with_a_passphrase()
    {
        $transaction = $this->getTransactionFixture(6, 'passphrase');

        $actual = Serializer::new($transaction['data'])->serialize();

        $this->assertSame($transaction['serialized'], $actual->getHex());
    }

    /** @test */
    public function it_should_serialize_the_transaction_with_a_second_passphrase()
    {
        $transaction = $this->getTransactionFixture(6, 'second-passphrase');

        $actual = Serializer::new($transaction['data'])->serialize();

        $this->assertSame($transaction['serialized'], $actual->getHex());
    }
}
