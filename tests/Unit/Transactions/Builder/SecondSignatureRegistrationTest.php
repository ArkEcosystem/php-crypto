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

namespace ArkEcosystem\Tests\Crypto\Unit\Transactions\Builder;

use ArkEcosystem\Crypto\Identities\PublicKey;
use ArkEcosystem\Crypto\Transactions\Builder\SecondSignatureRegistrationBuilder;
use ArkEcosystem\Crypto\Transactions\Serializer;

it('should create a valid transaction', function () {
    $transaction = SecondSignatureRegistrationBuilder::new()
        ->signature('this is a top secret second passphrase')
        ->sign('this is a top secret passphrase');

    $this->assertTrue($transaction->verify());
    $this->assertFalse(isset($transaction->secondSignature));
    $this->assertSame($transaction->transaction->data['asset']['signature']['publicKey'], PublicKey::fromPassphrase('this is a top secret second passphrase')->getHex());
});

it('should match fixture passphrase', function () {
    $fixture = $this->getTransactionFixture('second_signature_registration', 'second-signature-registration');
    $builder = SecondSignatureRegistrationBuilder::new()
        ->signature($this->secondPassphrase)
        ->sign($this->passphrase);

    $this->assertTrue($builder->verify());
    $this->assertSame($fixture['serialized'], Serializer::new($builder->transaction)->serialize()->getHex());
    $this->assertSameTransactions($fixture, $builder->transaction->data);
});
