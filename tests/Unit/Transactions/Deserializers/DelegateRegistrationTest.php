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

namespace ArkEcosystem\Tests\Crypto\Unit\Transactions\Deserializers;

use ArkEcosystem\Crypto\Transactions\Types\DelegateRegistration;

it('should deserialize the transaction signed with a passphrase', function () {
    $fixture = $this->getTransactionFixture('delegate_registration', 'delegate-registration-sign');

    assertTransactionForDelegateRegistration($fixture);
});

it('should deserialize the transaction signed with a second passphrase', function () {
    $fixture = $this->getTransactionFixture('delegate_registration', 'delegate-registration-secondSign');

    $actual = assertTransactionForDelegateRegistration($fixture);
    $this->assertSame($fixture['data']['secondSignature'], $actual->data['secondSignature']);
});

function assertTransactionForDelegateRegistration(array $fixture): DelegateRegistration
{
    $actual = test()->assertDeserialized($fixture, [
        'version',
        'network',
        'type',
        'typeGroup',
        'nonce',
        'senderPublicKey',
        'fee',
        'asset',
        'signature',
        'secondSignature',
        'amount',
        'id',
    ]);

    test()->assertTrue($actual->verify());

    return $actual;
}
