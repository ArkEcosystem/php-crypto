<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions\Builder;

use ArkEcosystem\Crypto\Enums\AbiFunction;
use ArkEcosystem\Crypto\Enums\ContractAbiType;
use ArkEcosystem\Crypto\Enums\ContractAddresses;
use ArkEcosystem\Crypto\Helpers;
use ArkEcosystem\Crypto\Transactions\Types\AbstractTransaction;
use ArkEcosystem\Crypto\Transactions\Types\EvmCall;
use ArkEcosystem\Crypto\Utils\AbiEncoder;
use Brick\Math\BigDecimal;
use Exception;

class BatchTransferBuilder extends AbstractTransactionBuilder
{
    private ?string $tokenAddress = null;

    /** @var array<int, array{recipient: string, amount: BigDecimal}> */
    private array $recipients = [];

    public function __construct(?array $data = null)
    {
        parent::__construct($data);

        $this->to(ContractAddresses::BATCH_TRANSFER->value);
    }

    public function tokenAddress(string $tokenAddress): self
    {
        $this->tokenAddress = $tokenAddress;

        return $this;
    }

    public function addRecipient(string $recipient, BigDecimal $amount): self
    {
        $this->recipients[] = ['recipient' => $recipient, 'amount' => $amount];

        return $this;
    }

    public function sign(string $passphrase): static
    {
        $this->encode();

        return parent::sign($passphrase);
    }

    protected function getTransactionInstance(array $data): AbstractTransaction
    {
        return new EvmCall($data);
    }

    private function encode(): void
    {
        if (count($this->recipients) === 0) {
            throw new Exception('Must add at least one recipient before encoding.');
        }

        if ($this->tokenAddress === null) {
            throw new Exception('Must set tokenAddress before encoding.');
        }

        $addresses = array_map(fn (array $recipient) => $recipient['recipient'], $this->recipients);
        $amounts   = array_map(fn (array $recipient) => $recipient['amount'], $this->recipients);

        $payload = (new AbiEncoder(ContractAbiType::ERC20BATCH_TRANSFER))->encodeFunctionCall(
            AbiFunction::BATCH_TRANSFER_FROM->value,
            [$this->tokenAddress, $addresses, $amounts]
        );

        $this->transaction->data['data'] = Helpers::removeLeadingHexZero($payload);
    }
}
