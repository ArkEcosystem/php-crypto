<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Enums;

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
