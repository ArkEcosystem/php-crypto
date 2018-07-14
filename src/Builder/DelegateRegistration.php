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

use ArkEcosystem\Crypto\Identity\PrivateKey;

/**
 * This is the delegate registration transaction class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class DelegateRegistration extends AbstractTransaction
{
    /**
     * Create a new delegate registration transaction instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->transaction->asset = ['delegate' => []];
    }

    /**
     * Set the username to assign.
     *
     * @param string $username
     *
     * @return \ArkEcosystem\Crypto\Builder\DelegateRegistration
     */
    public function username(string $username): self
    {
        $this->transaction->asset['delegate']['username'] = $username;

        return $this;
    }

    /**
     * Sign the transaction using the given passphrase.
     *
     * @param string $passphrase
     *
     * @return \ArkEcosystem\Crypto\Builder\AbstractTransaction
     */
    public function sign(string $passphrase): AbstractTransaction
    {
        $keys                                              = PrivateKey::fromPassphrase($passphrase);
        $this->transaction->senderPublicKey                = $keys->getPublicKey()->getHex();
        $this->transaction->asset['delegate']['publicKey'] = $this->transaction->senderPublicKey;

        $this->transaction = $this->transaction->sign($keys);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function getType(): int
    {
        return \ArkEcosystem\Crypto\Enums\Types::DELEGATE_REGISTRATION;
    }
}
