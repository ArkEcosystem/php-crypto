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

namespace ArkEcosystem\Crypto\Transactions\Builder;

use ArkEcosystem\Crypto\Identities\PublicKey;
use ArkEcosystem\Crypto\Transactions\Types\SecondSignatureRegistration;

/**
 * This is the second signature registration transaction class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class SecondSignatureRegistrationBuilder extends AbstractTransactionBuilder
{
    /**
     * Set the signature asset to register the second passphrase.
     *
     * @param string $secondPassphrase
     *
     * @return self
     */
    public function signature(string $secondPassphrase): self
    {
        $this->transaction->data['asset'] = [
            'signature' => [
                'publicKey' => PublicKey::fromPassphrase($secondPassphrase)->getHex(),
            ],
        ];

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function getType(): int
    {
        return \ArkEcosystem\Crypto\Enums\Types::SECOND_SIGNATURE_REGISTRATION;
    }

    protected function getTypeGroup(): int
    {
        return \ArkEcosystem\Crypto\Enums\TypeGroup::CORE;
    }

    protected function getTransactionInstance(): object
    {
        return new SecondSignatureRegistration();
    }
}
