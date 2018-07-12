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
 * This is the multi signature registration deserializer test class.
 *
 * @author Brian Faust <brian@ark.io>
 * @coversNothing
 */
class MultiSignatureRegistrationTest extends TestCase
{
    /** @test */
    public function it_should_deserialize_the_transaction_signed_with_a_passphrase()
    {
        $transaction = $this->getTransactionFixtureWithPassphrase(4);

        $actual = Deserializer::new($transaction->serialized)->deserialize();

        $this->assertSame($transaction->data->id, $actual->id);
        $this->assertSame($transaction->data->version, $actual->version);
        $this->assertSame($transaction->data->network, $actual->network);
        $this->assertSame($transaction->data->type, $actual->type);
        $this->assertSame($transaction->data->senderPublicKey, $actual->senderPublicKey);
        $this->assertSame($transaction->serialized, Serializer::new($actual)->serialize()->getHex());
    }
}
