<?php

declare(strict_types=1);

/*
 * This file is part of Ark PHP Crypto.
 *
 * (c) Ark Ecosystem <info@ark.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ArkEcosystem\Tests\Crypto\Unit\Utils;

use ArkEcosystem\Crypto\Utils\Message;

it('should sign a valid message', function () {
    $fixture = $this->getFixture('message-v1');

    $message = Message::sign($fixture['data']['message'], $fixture['passphrase']);

    $this->assertSame($message->publicKey, $fixture['data']['publickey']);
    $this->assertSame($message->signature, $fixture['data']['signature']);
    $this->assertSame($message->message, $fixture['data']['message']);
});

it('should create a message from an object', function () {
    $fixture = json_decode(json_encode($this->getFixture('message-v1')['data']));

    $message = Message::new($fixture);

    $this->assertSame($message->publicKey, $fixture->publickey);
    $this->assertSame($message->signature, $fixture->signature);
    $this->assertSame($message->message, $fixture->message);
});

it('should create a message from an array', function () {
    $fixture = $this->getFixture('message-v1')['data'];

    $message = Message::new($fixture);

    $this->assertSame($message->publicKey, $fixture['publickey']);
    $this->assertSame($message->signature, $fixture['signature']);
    $this->assertSame($message->message, $fixture['message']);
});

it('should create a message from a string', function () {
    $fixture = $this->getFixture('message-v1')['data'];

    $message = Message::new(json_encode($fixture));

    $this->assertSame($message->publicKey, $fixture['publickey']);
    $this->assertSame($message->signature, $fixture['signature']);
    $this->assertSame($message->message, $fixture['message']);
});

it('should not create a message from an invalid Type', function () {
    $this->expectException(\InvalidArgumentException::class);

    Message::new(false);
});

it('should sign a message', function () {
    $message = Message::sign('Hello World', 'passphrase');

    $this->assertInstanceOf(Message::class, $message);
});

it('should verify a message from v1', function () {
    $message = Message::new($this->getFixture('message-v1')['data']);

    $this->assertTrue($message->verify());
});

it('should verify a message from v2', function () {
    $message = Message::new($this->getFixture('message-v2')['data']);

    $this->assertTrue($message->verify());
});

it('should turn a message into an array', function () {
    $message = Message::new($this->getFixture('message-v1')['data']);

    $this->assertIsArray($message->toArray());
});

it('should turn a message into json', function () {
    $message = Message::new($this->getFixture('message-v1')['data']);

    $this->assertIsString($message->toJSON());
});

it('should turn a message into a string', function () {
    $message = Message::new($this->getFixture('message-v1')['data']);

    $this->assertIsString($message->__toString());
});

it('should sign and verify a message that is not valid utf8', function () {
    $message = Message::sign("\xff\xfe binary", 'passphrase');

    $this->assertSame("\xff\xfe binary", $message->message);
    $this->assertTrue($message->verify());
});

it('should not verify a tampered message', function () {
    $message = Message::new($this->getFixture('message-v2')['data']);
    $message->message .= '!';

    $this->assertFalse($message->verify());
});
