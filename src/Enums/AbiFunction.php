<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Enums;

use ArkEcosystem\Crypto\Transactions\Types\Unvote;
use ArkEcosystem\Crypto\Transactions\Types\ValidatorRegistration;
use ArkEcosystem\Crypto\Transactions\Types\ValidatorResignation;
use ArkEcosystem\Crypto\Transactions\Types\Vote;

enum AbiFunction: string
{
    case VOTE                         = 'vote';
    case UNVOTE                       = 'unvote';
    case VALIDATOR_REGISTRATION       = 'registerValidator';
    case VALIDATOR_RESIGNATION        = 'resignValidator';

    public function transactionClass(): string
    {
        return match ($this) {
            self::VOTE                       => Vote::class,
            self::UNVOTE                     => Unvote::class,
            self::VALIDATOR_REGISTRATION     => ValidatorRegistration::class,
            self::VALIDATOR_RESIGNATION      => ValidatorResignation::class,
        };
    }
}
