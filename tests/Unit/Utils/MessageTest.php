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
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * This is the message test class.
 *
 * @author Brian Faust <brian@ark.io>
 * @covers \ArkEcosystem\Crypto\Utils\Message
 */
class MessageTest extends TestCase
{
    /** @test */
    public function it_should_sign_a_valid_message()
    {
        $fixture = $this->getFixture('message-v1');

        $message = Message::sign($fixture['data']['message'], $fixture['passphrase']);

        $this->assertSame($message->publicKey, $fixture['data']['publickey']);
        $this->assertSame($message->signature, $fixture['data']['signature']);
        $this->assertSame($message->message, $fixture['data']['message']);
    }

    /** @test */
    public function it_should_create_a_message_from_an_object()
    {
        $fixture = json_decode(json_encode($this->getFixture('message-v1')['data']));

        $message = Message::new($fixture);

        $this->assertSame($message->publicKey, $fixture->publickey);
        $this->assertSame($message->signature, $fixture->signature);
        $this->assertSame($message->message, $fixture->message);
    }

    /** @test */
    public function it_should_create_a_message_from_an_array()
    {
        $fixture = $this->getFixture('message-v1')['data'];

        $message = Message::new($fixture);

        $this->assertSame($message->publicKey, $fixture['publickey']);
        $this->assertSame($message->signature, $fixture['signature']);
        $this->assertSame($message->message, $fixture['message']);
    }

    /** @test */
    public function it_should_create_a_message_from_a_string()
    {
        $fixture = $this->getFixture('message-v1')['data'];

        $message = Message::new(json_encode($fixture));

        $this->assertSame($message->publicKey, $fixture['publickey']);
        $this->assertSame($message->signature, $fixture['signature']);
        $this->assertSame($message->message, $fixture['message']);
    }

    /** @test */
    public function it_should_not_create_a_message_from_an_invalid_Type()
    {
        $this->expectException(\InvalidArgumentException::class);

        Message::new(false);
    }

    /** @test */
    public function it_should_sign_a_message()
    {
        $message = Message::sign('Hello World', 'passphrase');

        $this->assertInstanceOf(Message::class, $message);
    }

    /** @test */
    public function it_should_verify_a_message_from_v1()
    {
        $message = Message::new($this->getFixture('message-v1')['data']);

        $this->assertTrue($message->verify());
    }

    /** @test */
    public function it_should_verify_a_message_from_v2()
    {
        $message = Message::new($this->getFixture('message-v2')['data']);

        $this->assertTrue($message->verify());
    }

    /** @test */
    public function it_should_turn_a_message_into_an_array()
    {
        $message = Message::new($this->getFixture('message-v1')['data']);

        $this->assertIsArray($message->toArray());
    }

    /** @test */
    public function it_should_turn_a_message_into_json()
    {
        $message = Message::new($this->getFixture('message-v1')['data']);

        $this->assertIsString($message->toJSON());
    }

    /** @test */
    public function it_should_turn_a_message_into_a_string()
    {
        $message = Message::new($this->getFixture('message-v1')['data']);

        $this->assertIsString($message->__toString());
    }
}
