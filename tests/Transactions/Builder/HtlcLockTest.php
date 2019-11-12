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

use ArkEcosystem\Tests\Crypto\TestCase;
use ArkEcosystem\Crypto\Identities\PublicKey;
use ArkEcosystem\Crypto\Transactions\Builder\HtlcLockBuilder;

/**
 * This is the delegate registration builder test class.
 *
 * @author Brian Faust <brian@ark.io>
 * @covers \ArkEcosystem\Crypto\Transactions\Builder\HtlcLock
 */
class HtlcLockTest extends TestCase
{
    /** @test */
    public function it_should_sign_it_with_a_passphrase()
    {
        $transaction = HtlcLockBuilder::new()
            ->htlcLockAsset(
                "0f128d401958b1b30ad0d10406f47f9489321017b4614e6cb993fc63913c5454",
                1,
                1
            )
            ->recipient("ANBkoGqWeTSiaEVgVzSKZd3jS7UWzv9PSo")
            ->sign($this->passphrase);

        $this->assertTrue($transaction->verify());
    }

    /** @test */
    public function it_should_sign_it_with_a_second_passphrase()
    {
        $secondPassphrase = 'this is a top secret second passphrase';

        $transaction = HtlcLockBuilder::new()
            ->htlcLockAsset(
                "0f128d401958b1b30ad0d10406f47f9489321017b4614e6cb993fc63913c5454",
                1,
                1
            )
            ->recipient("ANBkoGqWeTSiaEVgVzSKZd3jS7UWzv9PSo")
            ->sign($this->passphrase)
            ->secondSign($secondPassphrase);

        $this->assertTrue($transaction->verify());
        $this->assertTrue($transaction->secondVerify(PublicKey::fromPassphrase($secondPassphrase)->getHex()));
    }
}
