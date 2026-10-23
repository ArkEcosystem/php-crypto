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

it('should serialize the transaction with a passphrase', function () {
    $this->assertSerialized($this->getTransactionFixture('htlc_claim', 'htlc-claim-sign'));
});

it('should serialize the transaction with a second passphrase', function () {
    $this->assertSerialized($this->getTransactionFixture('htlc_claim', 'htlc-claim-secondSign'));
});
