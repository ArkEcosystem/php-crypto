<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions\Builder;

use ArkEcosystem\Crypto\Enums\AbiFunction;
use ArkEcosystem\Crypto\Enums\ContractAbiType;
use ArkEcosystem\Crypto\Helpers;
use ArkEcosystem\Crypto\Transactions\Types\AbstractTransaction;
use ArkEcosystem\Crypto\Transactions\Types\EvmCall;
use ArkEcosystem\Crypto\Utils\AbiEncoder;
use Brick\Math\BigDecimal;

class TokenTransferBuilder extends AbstractTransactionBuilder
{
    public function contractAddress(string $address): self
    {
        return $this->to($address);
    }

    public function recipient(string $address, BigDecimal $amount): self
    {
        $payload = (new AbiEncoder(ContractAbiType::TOKEN))->encodeFunctionCall(
            AbiFunction::TRANSFER->value,
            [$address, $amount]
        );

        $this->transaction->data['data'] = Helpers::removeLeadingHexZero($payload);

        return $this;
    }

    protected function getTransactionInstance(array $data): AbstractTransaction
    {
        return new EvmCall($data);
    }
}
