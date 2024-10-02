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
                'gasLimit' => 1000000,
                'payload'  => '',
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
        if ($payload && !str_starts_with($payload, '0x')) {
            $payload = '0x' . $payload;
        }

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
