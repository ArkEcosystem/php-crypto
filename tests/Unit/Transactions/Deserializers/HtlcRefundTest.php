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

use ArkEcosystem\Crypto\Transactions\Types\HtlcRefund;

it('should deserialize the transaction signed with a passphrase', function () {
    $fixture = $this->getTransactionFixture('htlc_refund', 'htlc-refund-sign');

    assertTransactionForHtlcRefund($fixture);
});

it('should deserialize the transaction signed with a second passphrase', function () {
    $fixture = $this->getTransactionFixture('htlc_refund', 'htlc-refund-secondSign');

    $actual = assertTransactionForHtlcRefund($fixture);
    $this->assertSame($fixture['data']['secondSignature'], $actual->data['secondSignature']);
});

function assertTransactionForHtlcRefund(array $fixture): HtlcRefund
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
