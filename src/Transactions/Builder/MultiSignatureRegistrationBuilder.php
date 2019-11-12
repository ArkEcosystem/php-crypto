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

use ArkEcosystem\Crypto\Transactions\Types\MultiSignatureRegistration;

/**
 * This is the multisignature registration transaction class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class MultiSignatureRegistrationBuilder extends AbstractTransactionBuilder
{
    /**
     * Create a new multi signature transaction instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->transaction->data['asset'] = ['multiSignature' => []];
    }

    /**
     * Set the minimum required signatures.
     *
     * @param int $min
     *
     * @return \ArkEcosystem\Crypto\Transactions\Builder\MultiSignatureRegistration
     */
    public function min(int $min): self
    {
        $this->transaction->data['asset']['multiSignature']['min'] = $min;

        return $this;
    }

    /**
     * Set the publicKeys of signatures.
     *
     * @param array $publicKeys
     *
     * @return \ArkEcosystem\Crypto\Transactions\Builder\MultiSignatureRegistration
     */
    public function publicKeys(array $publicKeys): self
    {
        $this->transaction->data['asset']['multiSignature']['publicKeys'] = $publicKeys;

        $this->transaction->data['fee'] = (count($publicKeys) + 1) * $this->getFee();

        return $this;
    }

    /**
     * Add a participant to the public keys list.
     *
     * @param string $publicKey
     *
     * @return \ArkEcosystem\Crypto\Transactions\Builder\MultiSignatureRegistration
     */
    public function participant(string $publicKey): self
    {
        if (! isset($this->transaction->data['asset']['multiSignature']['publicKeys'])) {
            $this->transaction->data['asset']['multiSignature']['publicKeys'] = [$publicKey];
        } else {
            array_push($this->transaction->data['asset']['multiSignature']['publicKeys'], $publicKey);
        }

        $this->transaction->data['fee'] =
            (count($this->transaction->data['asset']['multiSignature']['publicKeys']) + 1) * $this->getFee();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function getType(): int
    {
        return \ArkEcosystem\Crypto\Enums\Types::MULTI_SIGNATURE_REGISTRATION;
    }

    protected function getTypeGroup(): int
    {
        return \ArkEcosystem\Crypto\Enums\TypeGroup::CORE;
    }

    protected function getTransactionInstance(): object
    {
        return new MultiSignatureRegistration();
    }
}
