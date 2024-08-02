<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions\Builder;

use ArkEcosystem\Crypto\Configuration\Network;
use ArkEcosystem\Crypto\Identities\Address;
use ArkEcosystem\Crypto\Transactions\Types\Vote;

class VoteBuilder extends AbstractTransactionBuilder
{
    /**
     * Create a new multi signature transaction instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->transaction->data['asset'] = [];
    }

    /**
     * Set the votes to cast.
     *
     * @param array $votes
     *
     * @return self
     */
    public function votes(array $votes): self
    {
        $this->transaction->data['asset']['votes'] = $votes;

        return $this;
    }

    /**
     * Set the unvotes to cast.
     *
     * @param array $votes
     *
     * @return self
     */
    public function unvotes(array $unvotes): self
    {
        $this->transaction->data['asset']['unvotes'] = $unvotes;

        return $this;
    }

    /**
     * Sign the transaction using the given passphrase.
     *
     * @param string $passphrase
     *
     * @return self
     */
    public function sign(string $passphrase): AbstractTransactionBuilder
    {
        $this->transaction->data['recipientId'] = Address::fromPassphrase($passphrase, Network::get());

        parent::sign($passphrase);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function getType(): int
    {
        return \ArkEcosystem\Crypto\Enums\Types::VOTE->value;
    }

    protected function getTypeGroup(): int
    {
        return \ArkEcosystem\Crypto\Enums\TypeGroup::CORE;
    }

    protected function getTransactionInstance(): object
    {
        return new Vote();
    }
}
