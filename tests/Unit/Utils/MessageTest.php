<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\Identities\Address;
use ArkEcosystem\Crypto\Utils\Message;

test('it should sign a valid message', function () {
    $fixture = $this->getFixture('message-sign');

    $message = Message::sign($fixture['message'], $this->passphrase);

    expect($message->publicKey)->toBe($fixture['publicKey']);
    expect($message->signature)->toBe(substr($fixture['signature'], 2));
    expect($message->message)->toBe($fixture['message']);
});

test('it should create a message from an object', function () {
    $fixture = json_decode(json_encode($this->getFixture('message-sign')));

    $message = Message::new($fixture);

    expect($message->publicKey)->toBe($fixture->publicKey);
    expect($message->signature)->toBe($fixture->signature);
    expect($message->message)->toBe($fixture->message);
});

test('it should create a message from an array', function () {
    $fixture = $this->getFixture('message-sign');

    $message = Message::new($fixture);

    expect($message->publicKey)->toBe($fixture['publicKey']);
    expect($message->signature)->toBe($fixture['signature']);
    expect($message->message)->toBe($fixture['message']);
});

test('it should throw if a field is missing', function (string $field, string $label) {
    $fixture = $this->getFixture('message-sign');

    unset($fixture[$field]);

    expect(fn () => Message::new($fixture))
        ->toThrow(InvalidArgumentException::class, "The given message did not contain a valid {$label}.");
})->with([
    ['publicKey', 'public key'],
    ['signature', 'signature'],
    ['message', 'message'],
]);

test('it should throw if a field is not a string', function (string $field, string $label, mixed $value) {
    $fixture         = $this->getFixture('message-sign');
    $fixture[$field] = $value;

    expect(fn () => Message::new($fixture))
        ->toThrow(InvalidArgumentException::class, "The given message did not contain a valid {$label}.");
})->with([
    ['publicKey', 'public key', null],
    ['signature', 'signature', 123],
    ['message', 'message', ['Hello, world!']],
]);

test('it should throw if the string is not a JSON object', function (string $json) {
    expect(fn () => Message::new($json))
        ->toThrow(InvalidArgumentException::class, 'The given message was neither an object, array nor JSON.');
})->with([
    'invalid json' => 'not json',
    'json array'   => '["Hello, world!"]',
]);

test('it should create a message from a string', function () {
    $fixture = $this->getFixture('message-sign');

    $message = Message::new(json_encode($fixture));

    expect($message->publicKey)->toBe($fixture['publicKey']);
    expect($message->signature)->toBe($fixture['signature']);
    expect($message->message)->toBe($fixture['message']);
});

test('it should not create a message from an invalid Type', function () {
    expect(fn () => Message::new(false))
        ->toThrow(InvalidArgumentException::class);
});

test('it should sign a message', function () {
    $message = Message::sign('Hello World', 'passphrase');

    expect($message)->toBeInstanceOf(Message::class);
});

test('it should verify a message', function () {
    $message = Message::new($this->getFixture('message-sign'));

    expect($message->verify())->toBeTrue();
});

test('it should verify a message without the 0x prefix', function () {
    $fixture              = $this->getFixture('message-sign');
    $fixture['signature'] = substr($fixture['signature'], 2);

    expect(Message::new($fixture)->verify())->toBeTrue();
});

test('it should verify a signed message', function () {
    $message = Message::sign('Hello World', 'passphrase');

    expect($message->verify())->toBeTrue();
});

test('it should verify a message with a recovery id without the 27 offset', function () {
    $fixture              = $this->getFixture('message-sign');
    $fixture['signature'] = substr($fixture['signature'], 0, -2).'01';

    expect(Message::new($fixture)->verify())->toBeTrue();
});

test('it should not verify a message with an invalid signature', function (string $signature) {
    $fixture              = $this->getFixture('message-sign');
    $fixture['signature'] = $signature;

    expect(Message::new($fixture)->verify())->toBeFalse();
})->with([
    'wrong recovery id'  => fn () => substr($this->getFixture('message-sign')['signature'], 0, -2).'1b',
    'out of range v'     => fn () => substr($this->getFixture('message-sign')['signature'], 0, -2).'1d',
    'non-hex v'          => fn () => substr($this->getFixture('message-sign')['signature'], 0, -2).'zz',
    'missing v'          => fn () => substr($this->getFixture('message-sign')['signature'], 0, -2),
    'trailing chars'     => fn () => $this->getFixture('message-sign')['signature'].'00',
    'non-hex'            => str_repeat('z', 130),
    'too short'          => 'abcd',
    'empty'              => '',
    'r not on the curve' => str_repeat('0', 128).'1b',
]);

test('it should not verify a message signed by another key', function () {
    $fixture              = $this->getFixture('message-sign');
    $fixture['publicKey'] = Message::sign('Hello World', 'passphrase')->publicKey;

    expect(Message::new($fixture)->verify())->toBeFalse();
});

test('it should not verify a tampered message', function () {
    $fixture            = $this->getFixture('message-sign');
    $fixture['message'] = 'Hello, world?';

    expect(Message::new($fixture)->verify())->toBeFalse();
});

test('it should not verify a message with an invalid public key', function (string $publicKey) {
    $fixture              = $this->getFixture('message-sign');
    $fixture['publicKey'] = $publicKey;

    expect(Message::new($fixture)->verify())->toBeFalse();
})->with([
    'off-curve' => '02'.str_repeat('0', 63).'5',
    'non-hex'   => 'zz',
    'empty'     => '',
]);

test('it should not throw for a message that is not valid UTF-8', function () {
    $fixture            = $this->getFixture('message-sign');
    $fixture['message'] = "\xff\xfe";

    expect(Message::new($fixture)->verify())->toBeFalse();
});

test('it should sign and verify a message that is not valid UTF-8', function () {
    expect(Message::sign("\xff\xfe", 'passphrase')->verify())->toBeTrue();
});

test('it should recover the signer address', function () {
    $fixture = $this->getFixture('message-sign');

    expect(Message::recoverAddress($fixture['message'], $fixture['signature']))
        ->toBe(Address::fromPublicKey($fixture['publicKey']));
});

test('it should return null when recovering from an invalid signature', function () {
    expect(Message::recoverAddress('Hello, world!', 'abcd'))->toBeNull();
});

test('it should turn a message into an array', function () {
    $message = Message::new($this->getFixture('message-sign'));

    expect($message->toArray())->toBeArray();
});

test('it should turn a message into json', function () {
    $message = Message::new($this->getFixture('message-sign'));

    expect($message->toJSON())->toBeString();
});

test('it should turn a message into a string', function () {
    $message = Message::new($this->getFixture('message-sign'));

    expect((string) $message)->toBeString();
});

test('it should throw when converting a message that is not valid UTF-8 to json', function () {
    $message = Message::sign("\xff\xfe", 'passphrase');

    expect(fn () => $message->toJson())->toThrow(JsonException::class);
});
