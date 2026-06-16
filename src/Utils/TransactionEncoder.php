<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Utils;

use ArkEcosystem\Crypto\BLS\ProofOfPossession;
use ArkEcosystem\Crypto\Enums\AbiFunction;
use ArkEcosystem\Crypto\Enums\ContractAbiType;
use Brick\Math\BigDecimal;

class TransactionEncoder
{
    /**
     * @param string[] $recipients
     * @param BigDecimal[] $amounts
     *
     * @return string
     */
    public static function multiPayment(array $recipients, array $amounts): string
    {
        return (new AbiEncoder(ContractAbiType::MULTIPAYMENT))->encodeFunctionCall(
            AbiFunction::MULTIPAYMENT->value,
            [$recipients, $amounts]
        );
    }

    public static function tokenTransfer(string $recipientAddress, BigDecimal $amount): string
    {
        return (new AbiEncoder(ContractAbiType::TOKEN))->encodeFunctionCall(
            AbiFunction::TRANSFER->value,
            [$recipientAddress, $amount]
        );
    }

    public static function usernameRegistration(string $username): string
    {
        return (new AbiEncoder(ContractAbiType::USERNAMES))->encodeFunctionCall(
            AbiFunction::USERNAME_REGISTRATION->value,
            [$username]
        );
    }

    public static function usernameResignation(): string
    {
        return (new AbiEncoder(ContractAbiType::USERNAMES))->encodeFunctionCall(
            AbiFunction::USERNAME_RESIGNATION->value
        );
    }

    public static function validatorRegistration(string $passphrase): string
    {
        $pop = ProofOfPossession::fromMnemonic($passphrase);

        return (new AbiEncoder(ContractAbiType::CONSENSUS))->encodeFunctionCall(
            AbiFunction::VALIDATOR_REGISTRATION->value,
            [self::addHexPrefix($pop['pk']), self::addHexPrefix($pop['pop'])]
        );
    }

    public static function validatorUpdate(string $passphrase): string
    {
        $pop = ProofOfPossession::fromMnemonic($passphrase);

        return (new AbiEncoder(ContractAbiType::CONSENSUS))->encodeFunctionCall(
            AbiFunction::VALIDATOR_UPDATE->value,
            [self::addHexPrefix($pop['pk']), self::addHexPrefix($pop['pop'])]
        );
    }

    public static function validatorResignation(): string
    {
        return (new AbiEncoder(ContractAbiType::CONSENSUS))->encodeFunctionCall(
            AbiFunction::VALIDATOR_RESIGNATION->value
        );
    }

    public static function vote(string $voteAddress): string
    {
        return (new AbiEncoder(ContractAbiType::CONSENSUS))->encodeFunctionCall(
            AbiFunction::VOTE->value,
            [$voteAddress]
        );
    }

    public static function unvote(): string
    {
        return (new AbiEncoder(ContractAbiType::CONSENSUS))->encodeFunctionCall(
            AbiFunction::UNVOTE->value
        );
    }

    private static function addHexPrefix(string $value): string
    {
        if (str_starts_with($value, '0x')) {
            return $value;
        }

        return '0x'.$value;
    }
}
