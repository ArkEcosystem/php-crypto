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
 * This is the multi payment transaction class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class MultiPayment extends AbstractTransaction
{
    /**
     * Create a new multi signature transaction instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->transaction->asset = ['payments' => []];
    }

    /**
     * Add a new payment to the collection.
     *
     * @param string $recipientId
     * @param int    $amount
     *
     * @return \ArkEcosystem\Crypto\Builder\MultiPayment
     */
    public function add(string $recipientId, int $amount): self
    {
        $this->transaction->asset['payments'][] = compact('recipientId', 'amount');

        return $this;
    }
}
