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

namespace ArkEcosystem\Tests\Crypto;

use ArkEcosystem\Crypto\Builder\SecondSignatureRegistration;
use ArkEcosystem\Crypto\Crypto;
use ArkEcosystem\Crypto\Identity\PublicKey;

/**
 * This is the second signature registration transaction test class.
 *
 * @author Brian Faust <brian@ark.io>
 * @coversNothing
 */
class SecondSignatureRegistrationTest extends TestCase
{
    /** @test */
    public function it_should_create_a_valid_transaction()
    {
        $transaction = SecondSignatureRegistration::create()
            ->signature('second passphrase')
            ->sign('first passphrase');

        $this->assertInternalType('object', $transaction);
        $this->assertTrue($transaction->verify());
        $this->assertFalse(isset($transaction->signSignature));
        $this->assertSame($transaction->data->asset['signature']['publicKey'], PublicKey::fromSecret('second passphrase')->getHex());
    }
}
