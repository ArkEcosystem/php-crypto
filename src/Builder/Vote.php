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

namespace ArkEcosystem\Crypto\Builder;

use ArkEcosystem\Crypto\Crypto;
use ArkEcosystem\Crypto\Enums\Fees;
use ArkEcosystem\Crypto\Enums\Types;
use ArkEcosystem\Crypto\Identity\Address;

/**
 * This is the vote transaction class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class Vote extends Transaction
{
    /**
     * Create a new vote transaction instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->data->type   = Types::VOTE;
        $this->data->fee    = Fees::VOTE;
        $this->data->amount = 0;
    }

    /**
     * Set the votes to cast.
     *
     * @param array $votes
     *
     * @return \ArkEcosystem\Crypto\Builder\Vote
     */
    public function votes(array $votes): self
    {
        $this->data->asset = compact('votes');

        return $this;
    }

    /**
     * Sign the transaction using the given secret.
     *
     * @param string $secret
     *
     * @return \ArkEcosystem\Crypto\Builder\Transaction
     */
    public function sign(string $secret): Transaction
    {
        $this->data->recipientId = Address::fromSecret($secret, $this->network);

        parent::sign($secret);

        return $this;
    }
}
