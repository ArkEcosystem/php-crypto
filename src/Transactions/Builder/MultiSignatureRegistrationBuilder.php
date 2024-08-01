<?php

declare(strict_types=1);



namespace ArkEcosystem\Crypto\Transactions\Builder;

use ArkEcosystem\Crypto\Transactions\Types\MultiSignatureRegistration;
use Illuminate\Support\Arr;

/**
 * This is the multisignature registration transaction class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class MultiSignatureRegistrationBuilder extends AbstractTransactionBuilder
{
    /**
     * Create a new multi signature transaction instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->transaction->data['asset']     = ['multiSignature' => [
            'min'        => 0,
            'publicKeys' => [],
        ]];
    }

    /**
     * Set the minimum required signatures.
     *
     * @param int $min
     *
     * @return self
     */
    public function min(int $min): self
    {
        $this->transaction->data['asset']['multiSignature']['min'] = $min;

        return $this;
    }

    /**
     * Add a participant to the multi signature registration.
     *
     * @param string $publicKey
     *
     * @return self
     */
    public function participant(string $publicKey): self
    {
        if (Arr::get($this->transaction->data, 'asset.multiSignature.publicKeys', []) <= 16) {
            $this->transaction->data['asset']['multiSignature']['publicKeys'][] = $publicKey;
        }

        return $this;
    }

    /**
     * Set the multiSignature asset for the transaction.
     *
     * @param array{
     *     min: int,
     *     publicKeys: string[]
     * } $asset The multiSignature asset array containing 'min' and 'publicKeys'.
     *
     * @return self
     */
    public function multiSignatureAsset(array $asset): self
    {
        Arr::set($this->transaction->data, 'asset.multiSignature', $asset);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function getType(): int
    {
        return \ArkEcosystem\Crypto\Enums\Types::MULTI_SIGNATURE_REGISTRATION->value;
    }

    /**
     * {@inheritdoc}
     */
    protected function getTypeGroup(): int
    {
        return \ArkEcosystem\Crypto\Enums\TypeGroup::CORE;
    }

    protected function getTransactionInstance(): object
    {
        return new MultiSignatureRegistration();
    }
}
