<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Enums;

use ArkEcosystem\Crypto\Transactions\Types\Multipayment;
use ArkEcosystem\Crypto\Transactions\Types\Unvote;
use ArkEcosystem\Crypto\Transactions\Types\UsernameRegistration;
use ArkEcosystem\Crypto\Transactions\Types\UsernameResignation;
use ArkEcosystem\Crypto\Transactions\Types\ValidatorRegistration;
use ArkEcosystem\Crypto\Transactions\Types\ValidatorResignation;
use ArkEcosystem\Crypto\Transactions\Types\Vote;

enum AbiFunction: string
{
    case VOTE                         = 'vote';
    case UNVOTE                       = 'unvote';
    case VALIDATOR_REGISTRATION       = 'registerValidator';
    case VALIDATOR_RESIGNATION        = 'resignValidator';
    case USERNAME_REGISTRATION        = 'registerUsername';
    case USERNAME_RESIGNATION         = 'resignUsername';
    case MULTIPAYMENT                 = 'pay';

    public function transactionClass(): string
    {
        return match ($this) {
            self::VOTE                       => Vote::class,
            self::UNVOTE                     => Unvote::class,
            self::VALIDATOR_REGISTRATION     => ValidatorRegistration::class,
            self::VALIDATOR_RESIGNATION      => ValidatorResignation::class,
            self::USERNAME_REGISTRATION      => UsernameRegistration::class,
            self::USERNAME_RESIGNATION       => UsernameResignation::class,
            self::MULTIPAYMENT               => Multipayment::class,
        };
    }
}
