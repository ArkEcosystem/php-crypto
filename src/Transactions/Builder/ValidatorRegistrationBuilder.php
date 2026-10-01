<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions\Builder;

use ArkEcosystem\Crypto\BLS\ProofOfPossession;
use ArkEcosystem\Crypto\Configuration\Network;
use ArkEcosystem\Crypto\Enums\ContractAddresses;
use ArkEcosystem\Crypto\Transactions\Types\AbstractTransaction;
use ArkEcosystem\Crypto\Transactions\Types\ValidatorRegistration;
use Brick\Math\BigDecimal;

class ValidatorRegistrationBuilder extends AbstractTransactionBuilder
{
    public function __construct(?array $data = null)
    {
        parent::__construct($data);

        $this->to(ContractAddresses::CONSENSUS->value);
    }

    /**
     * The registrant address must be the one that signs this transaction.
     */
    public function proofOfPossession(string $validatorPassphrase, string $registrantAddress, ?int $chainId = null): self
    {
        $pop = ProofOfPossession::fromMnemonic(
            $validatorPassphrase,
            $chainId ?? Network::get()->chainId(),
            $registrantAddress,
        );

        $this->transaction->data['validatorPublicKey'] = $pop['pk'];
        $this->transaction->data['validatorProof']     = $pop['pop'];

        $this->transaction->refreshPayloadData();

        return $this;
    }

    public function value(BigDecimal $value): self
    {
        $this->transaction->data['value'] = $value;

        $this->transaction->refreshPayloadData();

        return $this;
    }

    protected function getTransactionInstance(array $data): AbstractTransaction
    {
        return new ValidatorRegistration($data);
    }
}
