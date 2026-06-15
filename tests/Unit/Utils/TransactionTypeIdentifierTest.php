<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\Utils\TransactionTypeIdentifier;

function loadMethodIdentifiers(string $path): array
{
    $json = file_get_contents($path);

    expect($json)->not->toBeFalse();

    $decoded = json_decode($json, true);

    expect($decoded)->toBeArray();
    expect($decoded)->toHaveKey('methodIdentifiers');

    return $decoded['methodIdentifiers'];
}

beforeEach(function () {
    $this->consensusMethods    = loadMethodIdentifiers(dirname(__DIR__, 3).'/src/Utils/Abi/json/Abi.Consensus.json');
    $this->multipaymentMethods = loadMethodIdentifiers(dirname(__DIR__, 3).'/src/Utils/Abi/json/Abi.Multipayment.json');
    $this->usernamesMethods    = loadMethodIdentifiers(dirname(__DIR__, 3).'/src/Utils/Abi/json/Abi.Usernames.json');
});

it('identifies transfer by empty payload', function () {
    expect(TransactionTypeIdentifier::isTransfer(''))->toBeTrue();
    expect(TransactionTypeIdentifier::isTransfer('0x'))->toBeFalse();
    expect(TransactionTypeIdentifier::isTransfer('12345678'))->toBeFalse();
});

it('identifies vote signature', function () {
    $signature = $this->consensusMethods['vote(address)'];

    expect(TransactionTypeIdentifier::isVote($signature))->toBeTrue();
    expect(TransactionTypeIdentifier::isVote('0x'.$signature))->toBeTrue();
    expect(TransactionTypeIdentifier::isVote('1234567'))->toBeFalse();
});

it('identifies unvote signature', function () {
    $signature = $this->consensusMethods['unvote()'];

    expect(TransactionTypeIdentifier::isUnvote($signature))->toBeTrue();
    expect(TransactionTypeIdentifier::isUnvote('0x'.$signature))->toBeTrue();
    expect(TransactionTypeIdentifier::isUnvote('1234567'))->toBeFalse();
});

it('identifies multipayment signature', function () {
    $signature = $this->multipaymentMethods['pay(address[],uint256[])'];

    expect(TransactionTypeIdentifier::isMultiPayment($signature))->toBeTrue();
    expect(TransactionTypeIdentifier::isMultiPayment('0x'.$signature))->toBeTrue();
    expect(TransactionTypeIdentifier::isMultiPayment('1234567'))->toBeFalse();
});

it('identifies username registration signature', function () {
    $signature = $this->usernamesMethods['registerUsername(string)'];

    expect(TransactionTypeIdentifier::isUsernameRegistration($signature))->toBeTrue();
    expect(TransactionTypeIdentifier::isUsernameRegistration('0x'.$signature))->toBeTrue();
    expect(TransactionTypeIdentifier::isUsernameRegistration('1234567'))->toBeFalse();
});

it('identifies username resignation signature', function () {
    $signature = $this->usernamesMethods['resignUsername()'];

    expect(TransactionTypeIdentifier::isUsernameResignation($signature))->toBeTrue();
    expect(TransactionTypeIdentifier::isUsernameResignation('0x'.$signature))->toBeTrue();
    expect(TransactionTypeIdentifier::isUsernameResignation('1234567'))->toBeFalse();
});

it('identifies validator registration signature', function () {
    $signature = $this->consensusMethods['registerValidator(bytes)'];

    expect(TransactionTypeIdentifier::isValidatorRegistration($signature))->toBeTrue();
    expect(TransactionTypeIdentifier::isValidatorRegistration('0x'.$signature))->toBeTrue();
    expect(TransactionTypeIdentifier::isValidatorRegistration('1234567'))->toBeFalse();
});

it('identifies validator resignation signature', function () {
    $signature = $this->consensusMethods['resignValidator()'];

    expect(TransactionTypeIdentifier::isValidatorResignation($signature))->toBeTrue();
    expect(TransactionTypeIdentifier::isValidatorResignation('0x'.$signature))->toBeTrue();
    expect(TransactionTypeIdentifier::isValidatorResignation('1234567'))->toBeFalse();
});

it('identifies update validator signature', function () {
    $signature = $this->consensusMethods['updateValidator(bytes)'];

    expect(TransactionTypeIdentifier::isUpdateValidator($signature))->toBeTrue();
    expect(TransactionTypeIdentifier::isUpdateValidator('0x'.$signature))->toBeTrue();
    expect(TransactionTypeIdentifier::isUpdateValidator('1234567'))->toBeFalse();
});

it('identifies token transfer payloads', function () {
    expect(TransactionTypeIdentifier::isTokenTransfer('0xa9059cbb000000000000000000000000a5cc0bfeb09742c5e4c610f2ebaab82eb142ca10000000000000000000000000000000000000009bd2ffdd71438a49e803314000'))->toBeTrue();
    expect(TransactionTypeIdentifier::isTokenTransfer('0x'.str_repeat('0', 64)))->toBeFalse();
    expect(TransactionTypeIdentifier::isTokenTransfer('1234567'))->toBeFalse();
});
