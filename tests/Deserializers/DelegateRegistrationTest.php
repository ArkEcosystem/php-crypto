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

namespace ArkEcosystem\Tests\Crypto\Deserializers;

use ArkEcosystem\Crypto\Deserializer;
use ArkEcosystem\Crypto\Serializer;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * This is the delegate registration deserializer test class.
 *
 * @author Brian Faust <brian@ark.io>
 * @coversNothing
 */
class DelegateRegistrationTest extends TestCase
{
    /** @test */
    public function it_should_deserialize_the_transaction_signed_with_a_passphrase()
    {
        $transaction = $this->getTransactionFixtureWithPassphrase(2);

        $actual = Deserializer::new($transaction['serialized'])->deserialize();

        $this->assertTransaction($transaction, $actual);
    }

    /** @test */
    public function it_should_deserialize_the_transaction_signed_with_a_second_passphrase()
    {
        $transaction = $this->getTransactionFixtureWithSecondPassphrase(2);

        $actual = Deserializer::new($transaction['serialized'])->deserialize();

        $this->assertTransaction($transaction, $actual);
        $this->assertSame($transaction['data']['signSignature'], $actual->signSignature);
    }

    private function assertTransaction($transaction, $actual)
    {
        $this->assertSame(1, $actual->version);
        $this->assertSame(30, $actual->network);
        $this->assertSame($transaction['data']['type'], $actual->type);
        $this->assertSame($transaction['data']['timestamp'], $actual->timestamp);
        $this->assertSame($transaction['data']['senderPublicKey'], $actual->senderPublicKey);
        $this->assertSame($transaction['data']['fee'], $actual->fee);
        $this->assertSame($transaction['data']['asset']['delegate']['username'], $actual->asset['delegate']['username']);
        $this->assertSame($transaction['data']['signature'], $actual->signature);
        $this->assertSame($transaction['data']['amount'], $actual->amount);
        $this->assertSame($transaction['data']['id'], $actual->id);
        $this->assertSame($transaction['serialized'], Serializer::new($actual->toArray())->serialize()->getHex());
    }
}
