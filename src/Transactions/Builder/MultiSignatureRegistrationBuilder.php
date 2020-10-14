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
use ArkEcosystem\Crypto\Utils\Slot;

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

        $this->transaction->data['asset']     = ['multiSignatureLegacy' => []];
        $this->transaction->data['version']   = 1; // legacy multisig until schnorr implementation (AIP 18)
        $this->transaction->data['timestamp'] = Slot::time(); // legacy multisig until schnorr implementation (AIP 18)
    }

    /**
     * Set the minimum required signatures.
     *
     * @param int $min
     *
     * @return self
     */
    public function min(int $min): self
    {
        $this->transaction->data['asset']['multiSignatureLegacy']['min'] = $min;

        return $this;
    }

    /**
     * Set the transaction lifetime.
     *
     * @param int $lifetime
     *
     * @return self
     */
    public function lifetime(int $lifetime): self
    {
        $this->transaction->data['asset']['multiSignatureLegacy']['lifetime'] = $lifetime;

        return $this;
    }

    /**
     * Set the keysgroup of signatures.
     *
     * @param array $keysgroup
     *
     * @return self
     */
    public function keysgroup(array $keysgroup): self
    {
        $this->transaction->data['asset']['multiSignatureLegacy']['keysgroup'] = $keysgroup;

        $this->transaction->data['fee'] = strval((count($keysgroup) + 1) * intval($this->transaction->data['fee']));

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function getType(): int
    {
        return \ArkEcosystem\Crypto\Enums\Types::MULTI_SIGNATURE_REGISTRATION;
    }

    /**
     * {@inheritdoc}
     */
    protected function getTypeGroup(): int
    {
        return \ArkEcosystem\Crypto\Enums\TypeGroup::CORE;
    }

    protected function getTransactionInstance(): object
    {
        return new MultiSignatureRegistration();
    }
}
