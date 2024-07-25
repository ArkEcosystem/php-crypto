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

namespace ArkEcosystem\Crypto\Transactions\Builder;

use ArkEcosystem\Crypto\Transactions\Types\ValidatorResignation;

/**
 * This is the valiadator resignation transaction class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class ValidatorResignationBuilder extends AbstractTransactionBuilder
{
    protected function getType(): int
    {
        return \ArkEcosystem\Crypto\Enums\Types::VALIDATOR_RESIGNATION;
    }

    protected function getTypeGroup(): int
    {
        return \ArkEcosystem\Crypto\Enums\TypeGroup::CORE;
    }

    protected function getTransactionInstance(): object
    {
        return new ValidatorResignation();
    }
}
