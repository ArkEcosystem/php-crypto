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

/**
 * This is the multisignature registration transaction class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class MultiSignatureRegistration extends AbstractTransaction
{
    /**
     * Create a new multi signature transaction instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->transaction->asset = ['multisignature' => []];
    }

    /**
     * Set the minimum required signatures.
     *
     * @param int $min
     *
     * @return \ArkEcosystem\Crypto\Builder\MultiSignatureRegistration
     */
    public function min(int $min): self
    {
        $this->transaction->asset['multisignature']['min'] = $min;

        return $this;
    }

    /**
     * Set the transaction lifetime.
     *
     * @param int $lifetime
     *
     * @return \ArkEcosystem\Crypto\Builder\MultiSignatureRegistration
     */
    public function lifetime(int $lifetime): self
    {
        $this->transaction->asset['multisignature']['lifetime'] = $lifetime;

        return $this;
    }

    /**
     * Set the keysgroup of signatures.
     *
     * @param array $keysgroup
     *
     * @return \ArkEcosystem\Crypto\Builder\MultiSignatureRegistration
     */
    public function keysgroup(array $keysgroup): self
    {
        $this->transaction->asset['multisignature']['keysgroup'] = $keysgroup;

        $this->transaction->fee = (count($keysgroup) + 1) * $this->transaction->fee;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function getType(): int
    {
        return \ArkEcosystem\Crypto\Enums\Types::MULTI_SIGNATURE_REGISTRATION;
    }
}
