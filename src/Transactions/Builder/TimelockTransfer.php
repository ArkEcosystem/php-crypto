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

/**
 * This is the timelock transfer transaction class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class TimelockTransfer extends Transfer
{
    /**
     * Set the timelock of the transfer.
     *
     * @param int $timelock
     *
     * @return \ArkEcosystem\Crypto\Transactions\Builder\TimelockTransfer
     */
    public function timelock(int $timelock): self
    {
        $this->transaction->timelock = $timelock;

        return $this;
    }

    /**
     * Set the timelock type of the transfer to timestamp.
     *
     * @return \ArkEcosystem\Crypto\Transactions\Builder\TimelockTransfer
     */
    public function timestamp(): self
    {
        $this->transaction->timelockType = 0x00;

        return $this;
    }

    /**
     * Set the timelock type of the transfer to block height.
     *
     * @return \ArkEcosystem\Crypto\Transactions\Builder\TimelockTransfer
     */
    public function height(): self
    {
        $this->transaction->timelockType = 0x01;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function getType(): int
    {
        return \ArkEcosystem\Crypto\Enums\Types::TIMELOCK_TRANSFER;
    }
}
