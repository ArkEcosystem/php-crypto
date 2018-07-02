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

namespace ArkEcosystem\Tests\Crypto;

use ArkEcosystem\Crypto\Crypto;
use ArkEcosystem\Crypto\Message;

/**
 * This is the message test class.
 *
 * @author Brian Faust <brian@ark.io>
 * @coversNothing
 */
class MessageTest extends TestCase
{
    /** @test */
    public function it_should_create_a_message_from_an_array()
    {
        $rawMessage = $this->getRawMessage();

        $message = Message::new($rawMessage);

        $this->assertSame($message->publicKey, $rawMessage['publicKey']);
        $this->assertSame($message->signature, $rawMessage['signature']);
        $this->assertSame($message->message, $rawMessage['message']);
    }

    /** @test */
    public function it_should_create_a_message_from_a_string()
    {
        $rawMessage = $this->getRawMessage();

        $message = Message::new(json_encode($rawMessage));

        $this->assertSame($message->publicKey, $rawMessage['publicKey']);
        $this->assertSame($message->signature, $rawMessage['signature']);
        $this->assertSame($message->message, $rawMessage['message']);
    }

    /** @test */
    public function it_should_sign_a_message()
    {
        $message = Message::sign('Hello World', 'secret');

        $this->assertInstanceOf(Message::class, $message);
    }

    /** @test */
    public function it_should_verify_a_message()
    {
        $message = Message::new($this->getRawMessage());

        $this->assertTrue($message->verify());
    }

    /** @test */
    public function it_should_turn_a_message_into_an_array()
    {
        $message = Message::new($this->getRawMessage());

        $this->assertInternalType('array', $message->toArray());
    }

    /** @test */
    public function it_should_turn_a_message_into_a_string()
    {
        $message = Message::new($this->getRawMessage());

        $this->assertInternalType('string', $message->toJSON());
    }

    private function getRawMessage(): array
    {
        return [
            'publicKey' => '03be686ed7f0539affbaf634f3bcc2b235e8e220e7be57e9397ab1c14c39137eb4',
            'signature' => '304402206bc3a1ad7107caeeae96594e58d782fd2e90e421c0c50d9b748630a4c363e54e022068c9ea170cac8ea22b44af5ff27697c1c14d8bc5beedb7d5e48411f257489a38',
            'message'   => 'Hello World',
        ];
    }
}
