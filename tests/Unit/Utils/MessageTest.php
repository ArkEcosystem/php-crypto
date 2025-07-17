<?php

declare(strict_types=1);

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

test('it should throw if no public key is provided', function () {
    $fixture = $this->getFixture('message-sign');

    unset($fixture['publicKey']);

    Message::new($fixture);
})->throws(InvalidArgumentException::class, 'The given message did not contain a valid public key.');

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
    $fixture = $this->getFixture('message-sign');
    $fixture['signature'] = substr($fixture['signature'], 2);
    $message = Message::new($fixture);

    expect($message->verify())->toBeTrue();
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
