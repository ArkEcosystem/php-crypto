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

use BrianFaust\Binary\Buffer\Writer\Buffer as Writer;

/**
 * This is the serializer class.
 *
 * @author Brian Faust <brian@ark.io>
 */
abstract class AbstractSerializer
{
    /**
     * @var array
     */
    protected $transaction;

    /**
     * @var string
     */
    protected $buffer;

    /**
     * Create a new serializer instance.
     *
     * @param array  $transaction
     * @param string $buffer
     */
    public function __construct(array $transaction, Writer $buffer)
    {
        $this->transaction = $transaction;
        $this->buffer      = $buffer;
    }

    /*
     * Handle the serialisation of transaction data.
     *
     * @param string $buffer
     *
     * @return string
     */
    abstract public function serialize(): void;
}
