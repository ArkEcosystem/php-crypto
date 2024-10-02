<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions\Builder;

use ArkEcosystem\Crypto\Transactions\Types\EvmCall;

class EvmCallBuilder extends AbstractTransactionBuilder
{
    /**
     * Create a new EVM call transaction instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->transaction->data['asset'] = [
            'evmCall' => [
                'gasLimit' => 1000000,  // Default gas limit
                'payload'  => '',       // EVM code in hex format
            ],
        ];
    }

    /**
     * Set the payload for the EVM call.
     *
     * @param string $payload
     * @return self
     */
    public function payload(string $payload): self
    {
        $payload                                                = ltrim($payload, '0x');
        $this->transaction->data['asset']['evmCall']['payload'] = $payload;

        return $this;
    }

    /**
     * Set the gas limit for the EVM call.
     *
     * @param int $gasLimit
     * @return self
     */
    public function gasLimit(int $gasLimit): self
    {
        $this->transaction->data['asset']['evmCall']['gasLimit'] = $gasLimit;

        return $this;
    }

    /**
     * Set the recipient of the EVM call.
     *
     * @param string $recipientId
     * @return self
     */
    public function recipient(string $recipientId): self
    {
        $this->transaction->data['recipientId'] = $recipientId;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function getType(): int
    {
        return \ArkEcosystem\Crypto\Enums\Types::EVM_CALL->value;
    }

    /**
     * {@inheritdoc}
     */
    protected function getTypeGroup(): int
    {
        return \ArkEcosystem\Crypto\Enums\TypeGroup::CORE;
    }

    /**
     * {@inheritdoc}
     */
    protected function getTransactionInstance(): object
    {
        return new EvmCall();
    }
}
