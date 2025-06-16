<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\Transactions\Deserializer;
use ArkEcosystem\Crypto\Transactions\Types\EvmCall;
use ArkEcosystem\Crypto\Transactions\Types\Multipayment;
use ArkEcosystem\Crypto\Transactions\Types\Transfer;
use ArkEcosystem\Crypto\Transactions\Types\Unvote;
use ArkEcosystem\Crypto\Transactions\Types\UsernameRegistration;
use ArkEcosystem\Crypto\Transactions\Types\UsernameResignation;
use ArkEcosystem\Crypto\Transactions\Types\ValidatorRegistration;
use ArkEcosystem\Crypto\Transactions\Types\ValidatorResignation;
use ArkEcosystem\Crypto\Transactions\Types\Vote;

/*
 * @covers \ArkEcosystem\Crypto\Transactions\Deserializer
 */
it('should deserialize a transfer signed with a passphrase', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'transfer');

    $transaction = $this->assertTransaction($fixture);

    expect($transaction)->toBeInstanceOf(Transfer::class);
});

it('should deserialize a transfer signed with a passphrase with 0 value', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'transfer-0');

    $transaction = $this->assertTransaction($fixture);

    expect($transaction)->toBeInstanceOf(EvmCall::class);
});

it('should deserialize a vote signed with a passphrase', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'vote');

    $transaction = $this->assertTransaction($fixture);

    expect($transaction->data['vote'])->toEqual('0xC3bBE9B1CeE1ff85Ad72b87414B0E9B7F2366763');
    expect($transaction->data['hash'])->toEqual($fixture['data']['hash']);
    expect($transaction)->toBeInstanceOf(Vote::class);
});

it('should deserialize a unvote signed with a passphrase', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'unvote');

    $transaction = $this->assertTransaction($fixture);

    expect($transaction)->toBeInstanceOf(Unvote::class);
});

it('should deserialize a validator registration signed with a passphrase', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'validator-registration');

    $transaction = $this->assertTransaction($fixture);

    expect($transaction)->toBeInstanceOf(ValidatorRegistration::class);
});

it('should deserialize a validator resignation signed with a passphrase', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'validator-resignation');

    $transaction = $this->assertTransaction($fixture);

    expect($transaction)->toBeInstanceOf(ValidatorResignation::class);
});

it('should deserialize a username registration signed with a passphrase', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'username-registration');

    $transaction = $this->assertTransaction($fixture);

    expect($transaction)->toBeInstanceOf(UsernameRegistration::class);
});

it('should deserialize a username resignation signed with a passphrase', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'username-resignation');

    $transaction = $this->assertTransaction($fixture);

    expect($transaction)->toBeInstanceOf(UsernameResignation::class);
});

it('should deserialize a multipayment signed with a passphrase', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'multipayment');

    $transaction = $this->assertTransaction($fixture);

    expect($transaction)->toBeInstanceOf(Multipayment::class);
});

it('should use ByteBuffer::fromHex when there is no null-byte in the string', function () {
    // The string does not contain a null-byte
    $hexString = 'abcdef1234567890';
    $deserializer = new Deserializer($hexString);

    // Use reflection to access the private buffer property
    $reflection = new ReflectionClass($deserializer);
    $bufferProperty = $reflection->getProperty('buffer');
    $bufferProperty->setAccessible(true);
    $buffer = $bufferProperty->getValue($deserializer);

    // The buffer should be an instance of ByteBuffer
    expect($buffer)->toBeInstanceOf(\ArkEcosystem\Crypto\ByteBuffer\ByteBuffer::class);

    // The buffer should contain the hex string (converted to binary)
    expect($buffer->toString('hex'))->toContain($hexString);
});

it('should use ByteBuffer::fromBinary when there is a null-byte in the string', function () {
    // The string contains a null-byte
    $binaryString = "abc\0def"; // hex: 61626300646566
    $hexString = '61626300646566';
    $deserializer = new Deserializer($binaryString);

    // Use reflection to access the private buffer property
    $reflection = new ReflectionClass($deserializer);
    $bufferProperty = $reflection->getProperty('buffer');
    $bufferProperty->setAccessible(true);
    $buffer = $bufferProperty->getValue($deserializer);

    // The buffer should be an instance of ByteBuffer
    expect($buffer)->toBeInstanceOf(\ArkEcosystem\Crypto\ByteBuffer\ByteBuffer::class);

    // The buffer should contain the binary string
    expect($buffer->toString('hex'))->toBe($hexString);
});

it('should return null if no data value in transaction data', function () {
    expect(Deserializer::decodePayload([]))->toBeNull();
    expect(Deserializer::decodePayload(['data' => '']))->toBeNull();
});
