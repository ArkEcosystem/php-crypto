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

namespace ArkEcosystem\Tests\Crypto\Unit;

use ArkEcosystem\Crypto\Identities\Address;
use ArkEcosystem\Crypto\Identities\PrivateKey;
use ArkEcosystem\Crypto\Identities\PublicKey;
use ArkEcosystem\Crypto\Networks\Mainnet;
use ArkEcosystem\Crypto\Networks\Testnet;
use ArkEcosystem\Crypto\Utils\Message;

it('should derive the same identity as core', function (array $vector) {
    $privateKey = PrivateKey::fromPassphrase($vector['passphrase']);

    $this->assertSame($vector['privateKey'], $privateKey->getHex());
    $this->assertSame($vector['publicKey'], $privateKey->getPublicKey()->getHex());
    $this->assertSame($vector['address']['mainnet'], Address::fromPublicKey($vector['publicKey'], Mainnet::new()));
    $this->assertSame($vector['address']['devnet'], Address::fromPublicKey($vector['publicKey'], 30));
    $this->assertSame($vector['wif']['mainnet'], $privateKey->toWif(Mainnet::new()));
    $this->assertSame($vector['wif']['testnet'], $privateKey->toWif(Testnet::new()));
    $this->assertSame($vector['privateKey'], PrivateKey::fromWif($vector['wif']['testnet'], Testnet::new())->getHex());
})->with('core vectors');

it('should sign messages exactly like core', function (array $vector) {
    $signed = Message::sign($vector['message']['message'], $vector['passphrase']);

    $this->assertSame($vector['message']['signature'], $signed->signature);
})->with('core vectors');

it('should verify messages signed by core', function (array $vector) {
    $message = Message::new([
        'publicKey' => $vector['publicKey'],
        'signature' => $vector['message']['signature'],
        'message'   => $vector['message']['message'],
    ]);

    $this->assertTrue($message->verify());
    $this->assertFalse(PublicKey::fromHex($vector['publicKey'])->verify(hash('sha256', $vector['message']['message'].' ', true), $vector['message']['signature']));
})->with('core vectors');

dataset('core vectors', function () {
    $fixture = json_decode(file_get_contents(__DIR__.'/../fixtures/core-vectors.json'), true);

    return array_combine(
        array_map(fn (int $index) => "vector {$index}", array_keys($fixture['vectors'])),
        array_map(fn (array $vector) => [$vector], $fixture['vectors']),
    );
});
