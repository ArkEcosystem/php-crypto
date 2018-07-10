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

namespace ArkEcosystem\Crypto\Serializers;

/**
 * This is the serializer class.
 *
 * @author Brian Faust <brian@ark.io>
 */
abstract class AbstractSerializer
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
     * Create a new serializer instance.
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
    abstract public function serialize(): string;
}
