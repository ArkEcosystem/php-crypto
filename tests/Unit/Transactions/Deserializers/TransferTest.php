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

use ArkEcosystem\Crypto\Transactions\Types\Transfer;

it('should deserialize the transaction signed with a passphrase', function () {
    $fixture = $this->getTransactionFixture('transfer', 'transfer-sign');

    $actual = assertTransactionForTransfer($fixture);
    $this->assertSame(0, $actual->data['expiration']);
});

it('should deserialize the transaction signed with a second passphrase', function () {
    $fixture = $this->getTransactionFixture('transfer', 'transfer-secondSign');

    $actual = assertTransactionForTransfer($fixture);
    $this->assertSame($fixture['data']['secondSignature'], $actual->data['secondSignature']);
});

it('should deserialize the transaction signed with a passphrase and vendor field', function () {
    $fixture = $this->getTransactionFixture('transfer', 'transfer-with-vendor-field-sign');

    $actual = assertTransactionForTransfer($fixture);
    $this->assertSame($fixture['data']['vendorField'], $actual->data['vendorField']);
});

it('should deserialize the transaction signed with a second passphrase and vendor field', function () {
    $fixture = $this->getTransactionFixture('transfer', 'transfer-with-vendor-field-secondSign');

    $actual = assertTransactionForTransfer($fixture);
    $this->assertSame($fixture['data']['vendorField'], $actual->data['vendorField']);
});

function assertTransactionForTransfer(array $fixture): Transfer
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
