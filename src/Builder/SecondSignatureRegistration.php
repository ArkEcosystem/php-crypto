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

namespace ArkEcosystem\Crypto\Builder;

use ArkEcosystem\Crypto\Identity\PublicKey;

/**
 * This is the second signature registration transaction class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class SecondSignatureRegistration extends AbstractTransaction
{
    /**
     * Set the signature asset to register the second passphrase.
     *
     * @param string $secondSecret
     *
     * @return \ArkEcosystem\Crypto\Builder\SecondSignatureRegistration
     */
    public function signature(string $secondSecret): self
    {
        $this->transaction->asset = [
            'signature' => [
                'publicKey' => PublicKey::fromPassphrase($secondSecret)->getHex(),
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
}
