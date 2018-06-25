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

namespace ArkEcosystem\Crypto\Models;

use ArkEcosystem\Crypto\Enums\Types;
use BitWasp\Buffertools\Buffer;
use stdClass;

/**
 * This is the transaction class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class Transaction
{
    /**
     * Available transaction serialisation handlers.
     *
     * @var array
     */
    private $serialiseHandlers = [
        Types::TRANSFER                      => 'Transfer',
        Types::SECOND_SIGNATURE_REGISTRATION => 'SecondSignatureRegistration',
        Types::DELEGATE_REGISTRATION         => 'DelegateRegistration',
        Types::VOTE                          => 'Vote',
        Types::MULTI_SIGNATURE_REGISTRATION  => 'MultiSignatureRegistration',
        Types::IPFS                          => 'Ipfs',
        Types::TIMELOCK_TRANSFER             => 'TimelockTransfer',
        Types::MULTI_PAYMENT                 => 'MultiPayment',
        Types::DELEGATE_RESIGNATION          => 'DelegateResignation',
    ];

    /**
     * Create a new serialiser instance.
     *
     * @param object $data
     */
    private function __construct(object $data)
    {
        $this->data = $data;
    }

    /**
     * Create a new serialiser instance from an object.
     *
     * @param object $transaction
     *
     * @return object
     */
    public static function fromObject(object $transaction): self
    {
        return new static($transaction);
    }

    /**
     * Create a new serialiser instance from an array.
     *
     * @param array $transaction
     *
     * @return object
     */
    public static function fromArray(array $transaction): self
    {
        return $this->fromString(json_encode($transaction));
    }

    /**
     * Create a new serialiser instance from a string.
     *
     * @param string $transaction
     *
     * @return object
     */
    public static function fromString(string $transaction): self
    {
        return new static(json_decode($transaction));
    }

    /**
     * Perform AIP11 compliant serialisation.
     *
     * @return \BitWasp\Buffertools\Buffer
     */
    public function serialise(): Buffer
    {
        $serialiser = $this->serialiseHandlers[$this->data->type];
        $serialiser = "ArkEcosystem\\Crypto\\Serialisers\\$serialiser";

        return (new $serialiser($this->data))->serialise();
    }

    /**
     * Perform AIP11 compliant deserialisation.
     *
     * @return stdClass
     */
    public function deserialise(): stdClass
    {
        $deserialiser = $this->serialiseHandlers[$this->data->type];
        $deserialiser = "ArkEcosystem\\Crypto\\Deserialisers\\$deserialiser";

        return (new $deserialiser($this->data))->deserialise();
    }
}
