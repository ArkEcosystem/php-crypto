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

namespace ArkEcosystem\Crypto\Serialisers;

/**
 * This is the serialiser class.
 *
 * @author Brian Faust <brian@ark.io>
 */
abstract class AbstractSerialiser
{
    /**
     * @var object
     */
    protected $transaction;

    /**
     * @var string
     */
    protected $bytes;

    /**
     * Create a new serialiser instance.
     *
     * @param object $transaction
     * @param string $this->bytes
     */
    public function __construct(object $transaction, string $bytes)
    {
        $this->transaction = $transaction;
        $this->bytes       = $bytes;
    }

    /**
     * Handle the serialisation of transaction data.
     *
     * @param string $bytes
     *
     * @return string
     */
    abstract public function serialise(): string;
}
