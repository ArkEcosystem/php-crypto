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

namespace ArkEcosystem\Tests\Crypto\Transactions\Builder;

use ArkEcosystem\Crypto\Identity\PublicKey;
use ArkEcosystem\Crypto\Transactions\Builder\SecondSignatureRegistration;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * This is the second signature registration builder test class.
 *
 * @author Brian Faust <brian@ark.io>
 * @coversNothing
 */
class SecondSignatureRegistrationTest extends TestCase
{
    /** @test */
    public function it_should_create_a_valid_transaction()
    {
        $transaction = SecondSignatureRegistration::new()
            ->signature('second passphrase')
            ->sign('first passphrase');

        $this->assertTrue($transaction->verify());
        $this->assertFalse(isset($transaction->signSignature));
        $this->assertSame($transaction->transaction->asset['signature']['publicKey'], PublicKey::fromPassphrase('second passphrase')->getHex());
    }
}
