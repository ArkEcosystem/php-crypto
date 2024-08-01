<?php

declare(strict_types=1);



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
        return \ArkEcosystem\Crypto\Enums\Types::VALIDATOR_RESIGNATION->value;
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
