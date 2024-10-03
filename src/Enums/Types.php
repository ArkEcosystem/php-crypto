<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Enums;

use ArkEcosystem\Crypto\Transactions\Types\EvmCall;
use ArkEcosystem\Crypto\Transactions\Types\MultiPayment;
use ArkEcosystem\Crypto\Transactions\Types\MultiSignatureRegistration;
use ArkEcosystem\Crypto\Transactions\Types\Transfer;
use ArkEcosystem\Crypto\Transactions\Types\UsernameRegistration;
use ArkEcosystem\Crypto\Transactions\Types\UsernameResignation;
use ArkEcosystem\Crypto\Transactions\Types\ValidatorRegistration;
use ArkEcosystem\Crypto\Transactions\Types\ValidatorResignation;
use ArkEcosystem\Crypto\Transactions\Types\Vote;
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

    public function transactionClass(): string
    {
        return match ($this) {
            Types::TRANSFER                     => Transfer::class,
            Types::VALIDATOR_REGISTRATION       => ValidatorRegistration::class,
            Types::VOTE                         => Vote::class,
            Types::MULTI_SIGNATURE_REGISTRATION => MultiSignatureRegistration::class,
            Types::MULTI_PAYMENT                => MultiPayment::class,
            Types::VALIDATOR_RESIGNATION        => ValidatorResignation::class,
            Types::USERNAME_REGISTRATION        => UsernameRegistration::class,
            Types::USERNAME_RESIGNATION         => UsernameResignation::class,
            Types::EVM_CALL                     => EvmCall::class,
        };
    }

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
