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

use ArkEcosystem\Crypto\Enums\Fees;

/**
 * This is the ipfs transaction class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class IPFS extends Transaction
{
    /**
     * Create a new ipfs transaction instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->data->type = Types::IPFS;
        $this->data->fee  = Fees::IPFS;
    }
}
