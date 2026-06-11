<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\BLS\EIP2333;

// Known mnemonic and expected BLS private key from the TypeScript SDK tests
const EIP2333_MNEMONIC = 'abandon abandon abandon abandon abandon abandon abandon abandon abandon abandon abandon about';
// Expected public key (G1) hex for the derived private key → confirmed by TypeScript SDK
const DERIVED_PK_HEX = 'b7e5e1ea87f5be0ac7d97402a73002d302a3672f58aacb443cc5456190eac50f439f61b73556209de7624dfb535459e8';

it('derives a 64-byte BIP39 seed from mnemonic', function () {
    $seed = EIP2333::mnemonicToSeed(EIP2333_MNEMONIC);
    expect(strlen($seed))->toBe(64);
});

it('derives a 32-byte master key from seed', function () {
    $seed   = EIP2333::mnemonicToSeed(EIP2333_MNEMONIC);
    $master = EIP2333::deriveMaster($seed);
    expect(strlen($master))->toBe(32);
});

it('derives a 32-byte child key at index 0', function () {
    $seed   = EIP2333::mnemonicToSeed(EIP2333_MNEMONIC);
    $master = EIP2333::deriveMaster($seed);
    $child  = EIP2333::deriveChild($master, 0);
    expect(strlen($child))->toBe(32);
});

it('is deterministic for the same mnemonic', function () {
    $a = EIP2333::deriveBlsPrivateKey(EIP2333_MNEMONIC);
    $b = EIP2333::deriveBlsPrivateKey(EIP2333_MNEMONIC);
    expect(bin2hex($a))->toBe(bin2hex($b));
});

it('produces different keys for different mnemonics', function () {
    $a = EIP2333::deriveBlsPrivateKey(EIP2333_MNEMONIC);
    $b = EIP2333::deriveBlsPrivateKey('zoo zoo zoo zoo zoo zoo zoo zoo zoo zoo zoo wrong');
    expect(bin2hex($a))->not->toBe(bin2hex($b));
});
