<?php

declare(strict_types=1);



namespace ArkEcosystem\Crypto\Transactions\Builder;

use ArkEcosystem\Crypto\Transactions\Types\UsernameResignation;

/**
 * This is the username resignation transaction class.
 */
class UsernameResignationBuilder extends AbstractTransactionBuilder
{
    /**
     * Create a new username resignation transaction instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function getType(): int
    {
        return \ArkEcosystem\Crypto\Enums\Types::USERNAME_RESIGNATION->value;
    }

    protected function getTypeGroup(): int
    {
        return \ArkEcosystem\Crypto\Enums\TypeGroup::CORE;
    }

    protected function getTransactionInstance(): object
    {
        return new UsernameResignation();
    }
}
