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

namespace ArkEcosystem\Tests\Crypto\Builder;

use ArkEcosystem\Crypto\Builder\MultiSignatureRegistration;
use ArkEcosystem\Crypto\Utils\Crypto;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * This is the multi signature registration transaction test class.
 *
 * @author Brian Faust <brian@ark.io>
 * @coversNothing
 */
class MultiSignatureRegistrationTest extends TestCase
{
    /** @test */
    public function it_should_create_a_valid_transaction()
    {
        $transaction = MultiSignatureRegistration::create()
            ->min(2)
            ->lifetime(255)
            ->keysgroup([
                '03a02b9d5fdd1307c2ee4652ba54d492d1fd11a7d1bb3f3a44c4a05e79f19de933',
                '13a02b9d5fdd1307c2ee4652ba54d492d1fd11a7d1bb3f3a44c4a05e79f19de933',
                '23a02b9d5fdd1307c2ee4652ba54d492d1fd11a7d1bb3f3a44c4a05e79f19de933',
            ])
            ->sign('secret')
            ->secondSign('second secret');

        $this->assertInternalType('object', $transaction);
        $this->assertTrue($transaction->verify());
    }
}
