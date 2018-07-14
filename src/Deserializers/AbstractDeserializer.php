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

namespace ArkEcosystem\Crypto\Deserializers;

use ArkEcosystem\Crypto\Transaction;
use BrianFaust\Binary\Buffer\Reader\Buffer as Reader;

/**
 * This is the deserializer class.
 *
 * @author Brian Faust <brian@ark.io>
 */
abstract class AbstractDeserializer
{
    /**
     * @var string
     */
    protected $hex;

    /**
     * @var string
     */
    protected $buffer;

    /**
     * @var int
     */
    protected $assetOffset;

    /**
     * @var object
     */
    protected $transaction;

    /**
     * Create a new deserializer instance.
     *
     * @param string $hex
     * @param string $buffer
     * @param int    $assetOffset
     * @param object $transaction
     */
    public function __construct(string $hex, Reader $buffer, int $assetOffset, Transaction $transaction)
    {
        $this->hex         = $hex;
        $this->buffer      = $buffer;
        $this->assetOffset = $assetOffset;
        $this->transaction = $transaction;
    }

    /**
     * Handle the deserialisation of transaction data.
     *
     * @param int    $assetOffset
     * @param object $transaction
     *
     * @return object
     */
    abstract public function deserialize(): object;

    /**
     * Parse the signatures of the given transaction.
     *
     * @param int $startOffset
     *
     * @return object
     */
    protected function parseSignatures(int $startOffset): object
    {
        return $this->transaction->parseSignatures($this->hex, $startOffset);
    }
}
