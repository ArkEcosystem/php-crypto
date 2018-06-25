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

use stdClass;

/**
 * This is the multisignature registration transaction class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class MultiSignatureRegistration extends Transaction
{
    /**
     * Create a new multi signature transaction instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->data->asset                 = new stdClass();
        $this->data->asset->multisignature = new stdClass();
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
        $this->data->asset->multisignature->min = $min;

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
        $this->data->asset->multisignature->lifetime = $lifetime;

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
        $this->data->asset->multisignature->keysgroup = $keysgroup;

        $this->data->fee = (count($keysgroup) + 1) * $this->data->fee;

        return $this;
    }
}
