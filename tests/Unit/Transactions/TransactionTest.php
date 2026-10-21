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

namespace ArkEcosystem\Tests\Crypto\Unit\Transactions\Serializers;

use ArkEcosystem\Crypto\Binary\Bytes;
use ArkEcosystem\Crypto\Identities\PrivateKey;
use ArkEcosystem\Crypto\Identities\PublicKey;
use ArkEcosystem\Crypto\Transactions\Deserializer;
use ArkEcosystem\Crypto\Transactions\Types\Transfer;

it('should compute the id of the transaction', function () {
    $actual = getTransactionForTransaction()->getId();

    $this->assertSame('8fd1cf0490276edb9b3cba40bcbf9a7b0ce04b90e40ffe4704fc776b2bf8aabe', $actual);
});

it('should sign the transaction using a passphrase', function () {
    $privateKey = PrivateKey::fromPassphrase('this is a top secret passphrase');

    $transaction                    = getTransactionForTransaction();
    $transaction->data['signature'] = null;

    $this->assertEmpty($transaction->data['signature']);
    $transaction->sign($privateKey);
    $this->assertNotEmpty($transaction->data['signature']);
});

it('should sign the transaction using a second passphrase', function () {
    $privateKey = PrivateKey::fromPassphrase('this is a top secret second passphrase');

    $transaction                          = getTransactionForTransaction();
    $transaction->data['secondSignature'] = null;

    $this->assertEmpty($transaction->data['secondSignature']);
    $transaction->secondSign($privateKey);
    $this->assertNotEmpty($transaction->data['secondSignature']);
});

it('should verify the transaction', function () {
    $actual = getTransactionForTransaction()->verify();

    $this->assertTrue($actual);
});

it('should verify the transaction using a second public key', function () {
    $secondPassphrase = 'this is a top secret second passphrase';

    $secondPublicKey = PublicKey::fromPassphrase($secondPassphrase)->getHex();

    $actual = getTransactionForTransaction('transfer-secondSign')->secondVerify($secondPublicKey);

    $this->assertTrue($actual);
});

it('should turn the transaction to bytes', function () {
    $actual = getTransactionForTransaction()->getBytes();

    $this->assertInstanceOf(Bytes::class, $actual);
});

it('should turn the transaction to an array', function () {
    $actual = getTransactionForTransaction()->toArray();

    $this->assertIsArray($actual);
});

it('should turn the transaction to json', function () {
    $actual = getTransactionForTransaction()->toJson();

    $this->assertIsString($actual);
});

function getTransactionForTransaction($file = 'transfer-sign'): Transfer
{
    $fixture = test()->getTransactionFixture('transfer', $file);

    return Deserializer::new($fixture['serialized'])->deserialize();
}
