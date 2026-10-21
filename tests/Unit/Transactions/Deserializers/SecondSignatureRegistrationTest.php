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

use ArkEcosystem\Crypto\Transactions\Types\SecondSignatureRegistration;

it('should deserialize the transaction signed with a second passphrase', function () {
    $transaction = $this->getTransactionFixture('second_signature_registration', 'second-signature-registration');

    $actual = assertTransactionForSecondSignatureRegistration($transaction);
});

function assertTransactionForSecondSignatureRegistration(array $fixture): SecondSignatureRegistration
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
