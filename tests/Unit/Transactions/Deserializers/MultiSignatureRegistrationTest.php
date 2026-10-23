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

use ArkEcosystem\Crypto\Transactions\Types\MultiSignatureRegistration;

it('should deserialize the transaction signed with a passphrase', function () {
    //TODO fail to verify : fixture is schnorr
    $transaction = $this->getTransactionFixture('multi_signature_registration', 'multi-signature-registration');

    assertTransactionForMultiSignatureRegistration($transaction);
});

it('should deserialize the transaction signed with a second passphrase', function () {
    //TODO fixture
    $this->markTestIncomplete('This test has not been implemented yet.');
    //$transaction = $this->getTransactionFixture('multi_signature_registration', 'multi-signature-secondSign');
    //
    //assertTransactionForMultiSignatureRegistration($transaction);
});

function assertTransactionForMultiSignatureRegistration(array $fixture): MultiSignatureRegistration
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

    // TODO test()->assertTrue($actual->verify()); when AIP-18 with Schnorr implemented
    return $actual;
}
