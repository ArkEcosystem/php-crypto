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

use ArkEcosystem\Crypto\Builder\DelegateRegistration;
use ArkEcosystem\Crypto\Utils\Crypto;

/**
 * This is the delegate registration transaction test class.
 *
 * @author Brian Faust <brian@ark.io>
 * @coversNothing
 */
class DelegateRegistrationTest extends TestCase
{
    /** @test */
    public function it_should_create_a_valid_transaction()
    {
        $transaction = DelegateRegistration::create()
            ->username('polopolo')
            ->sign('This is a top secret passphrase');

        $this->assertInternalType('object', $transaction);
        $this->assertTrue($transaction->verify());
    }
}
