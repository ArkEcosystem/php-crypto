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

namespace ArkEcosystem\Tests\ArkCrypto;

use ArkEcosystem\ArkCrypto\Utils\Crypto;

/**
 * @coversNothing
 */
class TransactionBuilderTest extends TestCase
{
    /** @test */
    public function can_create_signed_transaction_object()
    {
        // Arrange...
        $amount = 133380000000;
        $recipientId = 'AXoXnFi4z1Z6aFvjEYkDVCtBGW2PaRiM25';
        $vendorField = 'This is a transaction from PHP';
        $secret = 'this is a top secret passphrase';

        // Act...
        $transaction = $this->transactionBuilder->createTransfer($recipientId, $amount, $vendorField, $secret);

        // Assert...
        $this->assertInstanceOf('stdClass', $transaction);
        $this->assertTrue(Crypto::verify($transaction));
    }

    /** @test */
    public function second_passphrase_verification()
    {
        // Arrange...
        $amount = 133380000000;
        $recipientId = 'AXoXnFi4z1Z6aFvjEYkDVCtBGW2PaRiM25';
        $vendorField = 'This is a transaction from PHP';
        $secret = 'this is a top secret passphrase';
        $secondSecret = 'this is a top secret second passphrase';

        // Act...
        $transaction = $this->transactionBuilder->createTransfer($recipientId, $amount, $vendorField, $secret, $secondSecret);

        // Assert...
        $this->assertInstanceOf('stdClass', $transaction);
        $this->assertTrue(Crypto::verify($transaction));
        $this->assertTrue(Crypto::secondVerify($transaction, Crypto::getKeys($secondSecret)->getPublicKey()->getHex()));
    }

    /** @test */
    public function can_create_add_delegate_transaction()
    {
        // Arrange...
        $secret = 'this is a top secret passphrase';
        $name = 'polopolo';

        // Act...
        $transaction = $this->transactionBuilder->createDelegate($name, $secret);

        // Assert...
        $this->assertTrue(Crypto::verify($transaction));
    }

    /** @test */
    public function can_create_multisignature_transaction()
    {
        // Arrange...
        $secret = 'secret';
        $secondSecret = 'second secret';
        $min = 2;
        $lifetime = 255;
        $keysgroup = [
            '03a02b9d5fdd1307c2ee4652ba54d492d1fd11a7d1bb3f3a44c4a05e79f19de933',
            '13a02b9d5fdd1307c2ee4652ba54d492d1fd11a7d1bb3f3a44c4a05e79f19de933',
            '23a02b9d5fdd1307c2ee4652ba54d492d1fd11a7d1bb3f3a44c4a05e79f19de933',
        ];

        // Act...
        $transaction = $this->transactionBuilder->createMultiSignature($secret, $secondSecret, $keysgroup, $lifetime, $min);

        // Assert...
        $this->assertInstanceOf('stdClass', $transaction);
        $this->assertTrue(Crypto::verify($transaction));
    }

    /** @test */
    public function creates_valid_second_signature_transaction()
    {
        // Arrange...
        $firstSecret = 'first passphrase';
        $secondSecret = 'second passphrase';

        // Act...
        $transaction = $this->transactionBuilder->createSecondSignature($secondSecret, $firstSecret);

        // Assert...
        $this->assertInstanceOf('stdClass', $transaction);

        $this->assertTrue(Crypto::verify($transaction));
        $this->assertNull($transaction->signSignature);
        $this->assertEquals($transaction->asset['signature']['publicKey'], Crypto::getKeys($secondSecret)->getPublicKey()->getHex());
    }

    /** @test */
    public function can_create_vote_transaction()
    {
        // Arrange...
        $secret = 'this is a top secret passphrase';
        $delegate = '034151a3ec46b5670a682b0a63394f863587d1bc97483b1b6c70eb58e7f0aed192';

        // Act...
        $transaction = $this->transactionBuilder->createVote(['+'.$delegate], $secret, null, null);

        // Assert...
        $this->assertInstanceOf('stdClass', $transaction);
        $this->assertTrue(Crypto::verify($transaction));
    }
}
