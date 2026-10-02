<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions\Builder;

use ArkEcosystem\Crypto\BLS\ProofOfPossession;
use ArkEcosystem\Crypto\Enums\ContractAddresses;
use ArkEcosystem\Crypto\Transactions\Types\AbstractTransaction;
use ArkEcosystem\Crypto\Transactions\Types\ValidatorUpdate;

class ValidatorUpdateBuilder extends AbstractTransactionBuilder
{
    public function __construct(?array $data = null)
    {
        parent::__construct($data);

        $this->to(ContractAddresses::CONSENSUS->value);
    }

    /**
     * The registrant address must be the one that signs this transaction.
     */
    public function validatorProof(string $passphrase, string $registrantAddress): self
    {
        $pop = ProofOfPossession::fromMnemonic($passphrase, $registrantAddress);

        $this->transaction->data['validatorPublicKey'] = $pop['pk'];
        $this->transaction->data['validatorProof']     = $pop['pop'];

        $this->transaction->refreshPayloadData();

        return $this;
    }

    protected function getTransactionInstance(array $data): AbstractTransaction
    {
        return new ValidatorUpdate($data);
    }
}
