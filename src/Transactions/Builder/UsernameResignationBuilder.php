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
        return \ArkEcosystem\Crypto\Enums\Types::USERNAME_RESIGNATION;
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
