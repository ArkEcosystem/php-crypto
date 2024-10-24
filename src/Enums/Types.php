<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Enums;

use ArkEcosystem\Crypto\Transactions\Transaction;
use ReflectionEnum;

/**
 * This is the transaction types enum.
 */
enum Types: int
{
    case TRANSFER                     = 0;
    case VALIDATOR_REGISTRATION       = 2;
    case VOTE                         = 3;
    case MULTI_SIGNATURE_REGISTRATION = 4;
    case MULTI_PAYMENT                = 6;
    case VALIDATOR_RESIGNATION        = 7;
    case USERNAME_REGISTRATION        = 8;
    case USERNAME_RESIGNATION         = 9;
    case EVM_CALL                     = 10;

    public function defaultFee(): string
    {
        return match ($this) {
            Types::TRANSFER                     => Fees::TRANSFER,
            Types::VALIDATOR_REGISTRATION       => Fees::VALIDATOR_REGISTRATION,
            Types::VOTE                         => Fees::VOTE,
            Types::MULTI_SIGNATURE_REGISTRATION => Fees::MULTI_SIGNATURE_REGISTRATION,
            Types::MULTI_PAYMENT                => Fees::MULTI_PAYMENT,
            Types::VALIDATOR_RESIGNATION        => Fees::VALIDATOR_RESIGNATION,
            Types::USERNAME_REGISTRATION        => Fees::USERNAME_REGISTRATION,
            Types::USERNAME_RESIGNATION         => Fees::USERNAME_RESIGNATION,
            Types::EVM_CALL                     => Fees::EVM,
        };
    }

    public static function fromValue(int $value): ?self
    {
        $enum = new ReflectionEnum(self::class);

        foreach ($enum->getCases() as $case) {
            if ($case->getValue()->value === $value) {
                return $case->getValue();
            }
        }

        throw new \InvalidArgumentException("Invalid value: {$value}");
    }
}
