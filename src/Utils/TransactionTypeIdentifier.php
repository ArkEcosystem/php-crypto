<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Utils;

use ArkEcosystem\Crypto\Enums\ContractAbiType;
use ArkEcosystem\Crypto\Helpers;

class TransactionTypeIdentifier
{
    private const TRANSFER_SIGNATURE = '';

    private static ?array $signatures = null;

    public static function isTransfer(string $data): bool
    {
        return $data === self::TRANSFER_SIGNATURE;
    }

    public static function isVote(string $data): bool
    {
        return self::startsWithSignature($data, self::signatures()['vote']);
    }

    public static function isUnvote(string $data): bool
    {
        return self::startsWithSignature($data, self::signatures()['unvote']);
    }

    public static function isMultiPayment(string $data): bool
    {
        return self::startsWithSignature($data, self::signatures()['multiPayment']);
    }

    public static function isUsernameRegistration(string $data): bool
    {
        return self::startsWithSignature($data, self::signatures()['registerUsername']);
    }

    public static function isUsernameResignation(string $data): bool
    {
        return self::startsWithSignature($data, self::signatures()['resignUsername']);
    }

    public static function isValidatorRegistration(string $data): bool
    {
        return self::startsWithSignature($data, self::signatures()['registerValidator']);
    }

    public static function isValidatorResignation(string $data): bool
    {
        return self::startsWithSignature($data, self::signatures()['resignValidator']);
    }

    public static function isUpdateValidator(string $data): bool
    {
        return self::startsWithSignature($data, self::signatures()['updateValidator']);
    }

    public static function isTokenTransfer(string $data): bool
    {
        $decodedData = static::decodeTokenFunction($data);

        return $decodedData ? $decodedData['functionName'] === 'transfer' : false;
    }

    private static function startsWithSignature(string $data, string $signature): bool
    {
        return str_starts_with(
            strtolower(Helpers::removeLeadingHexZero($data)),
            strtolower($signature)
        );
    }

    private static function signatures(): array
    {
        if (self::$signatures !== null) {
            return self::$signatures;
        }

        $consensusMethods    = AbiBase::methodIdentifiers(ContractAbiType::CONSENSUS);
        $multipaymentMethods = AbiBase::methodIdentifiers(ContractAbiType::MULTIPAYMENT);
        $usernamesMethods    = AbiBase::methodIdentifiers(ContractAbiType::USERNAMES);

        self::$signatures = [
            'multiPayment'      => $multipaymentMethods['pay(address[],uint256[])'],
            'registerUsername'  => $usernamesMethods['registerUsername(string)'],
            'resignUsername'    => $usernamesMethods['resignUsername()'],
            'registerValidator' => $consensusMethods['registerValidator(bytes,bytes)'],
            'resignValidator'   => $consensusMethods['resignValidator()'],
            'vote'              => $consensusMethods['vote(address)'],
            'unvote'            => $consensusMethods['unvote()'],
            'updateValidator'   => $consensusMethods['updateValidator(bytes,bytes)'],
            'transfer'          => 'transfer',
        ];

        return self::$signatures;
    }

    private static function decodeTokenFunction(string $data): ?array
    {
        try {
            $decodedData = (new AbiDecoder(ContractAbiType::TOKEN))->decodeFunctionData($data);

            return ['functionName' => $decodedData['functionName'], 'args' => $decodedData['args']];
        } catch (\Exception $e) {
            // Different abi type. Ignore.
        }

        return null;
    }
}
