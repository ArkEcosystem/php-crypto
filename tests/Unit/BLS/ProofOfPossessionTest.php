<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\BLS\ProofOfPossession;

// Test vectors from the TypeScript SDK ProofOfPossession.test.ts
const POP_SK_A_HEX   = '67d53f170b908cabb9eb326c3c337762d59289a8fec79f7bc9254b584b73265c';
const POP_SK_B_HEX   = '3325023a5e4e0069558c5bd9eb7eca78b4f4c7711b9b231d9263a8edc33bc510';
const POP_MNEMONIC_A = 'abandon abandon abandon abandon abandon abandon abandon abandon abandon abandon abandon about';

// Pinned vectors from noble-curves reference implementation
const EXPECTED_PK_HEX  = 'a7e75af9dd4d868a41ad2f5a5b021d653e31084261724fb40ae2f1b1c31c778d3b9464502d599cf6720723ec5c68b59d';
const EXPECTED_POP_HEX = '878ad02e1f215d40722bd77a0148adb8dfaad4514157600a0a926cfc58589fa4e79d3d4d579cc4149237b8100efdcff110dd2a251c52543539d499c8f24b142da66d1dc19ec44b3d9c3f71112b2705e5557f932a36bd9cd9b3544ab0d9e6a677';
const EXPECTED_MNEMONIC_PK = 'b7e5e1ea87f5be0ac7d97402a73002d302a3672f58aacb443cc5456190eac50f439f61b73556209de7624dfb535459e8';

// -------------------------------------------------------------------------
// G1 public key derivation
// -------------------------------------------------------------------------

it('produces a 96-char hex G1 public key from a private key', function () {
    $skBytes = hex2bin(POP_SK_A_HEX);
    $pk      = ProofOfPossession::privateKeyToPublicKey($skBytes);
    expect(strlen($pk))->toBe(96); // 48 bytes compressed G1
});

it('matches the pinned G1 pk for SK_A', function () {
    $pk = ProofOfPossession::privateKeyToPublicKey(hex2bin(POP_SK_A_HEX));
    expect($pk)->toBe(EXPECTED_PK_HEX);
});

it('produces a 96-char pk from a mnemonic', function () {
    $pk = ProofOfPossession::deriveBlsPublicKey(POP_MNEMONIC_A);
    expect(strlen($pk))->toBe(96);
});

it('matches the pinned pk for the canonical mnemonic', function () {
    $pk = ProofOfPossession::deriveBlsPublicKey(POP_MNEMONIC_A);
    expect($pk)->toBe(EXPECTED_MNEMONIC_PK);
});

// -------------------------------------------------------------------------
// Proof of Possession
// -------------------------------------------------------------------------

it('buildProofOfPossession returns 96-char pk and 192-char pop', function () {
    $result = ProofOfPossession::buildProofOfPossession(hex2bin(POP_SK_A_HEX));
    expect(strlen($result['pk']))->toBe(96);
    expect(strlen($result['pop']))->toBe(192); // 96 bytes compressed G2
});

it('pk in buildProofOfPossession matches deriveBlsPublicKey', function () {
    $result = ProofOfPossession::buildProofOfPossession(hex2bin(POP_SK_A_HEX));
    $pk     = ProofOfPossession::privateKeyToPublicKey(hex2bin(POP_SK_A_HEX));
    expect($result['pk'])->toBe($pk);
});

it('matches the pinned pk and pop for SK_A', function () {
    $result = ProofOfPossession::buildProofOfPossession(hex2bin(POP_SK_A_HEX));
    expect($result['pk'])->toBe(EXPECTED_PK_HEX);
    expect($result['pop'])->toBe(EXPECTED_POP_HEX);
});

it('is deterministic for the same secret key', function () {
    $a = ProofOfPossession::buildProofOfPossession(hex2bin(POP_SK_A_HEX));
    $b = ProofOfPossession::buildProofOfPossession(hex2bin(POP_SK_A_HEX));
    expect($a['pk'])->toBe($b['pk']);
    expect($a['pop'])->toBe($b['pop']);
});

it('produces different pk and pop for different secret keys', function () {
    $a = ProofOfPossession::buildProofOfPossession(hex2bin(POP_SK_A_HEX));
    $b = ProofOfPossession::buildProofOfPossession(hex2bin(POP_SK_B_HEX));
    expect($a['pk'])->not->toBe($b['pk']);
    expect($a['pop'])->not->toBe($b['pop']);
});

it('fromMnemonic is deterministic', function () {
    $a = ProofOfPossession::fromMnemonic(POP_MNEMONIC_A);
    $b = ProofOfPossession::fromMnemonic(POP_MNEMONIC_A);
    expect($a['pk'])->toBe($b['pk']);
    expect($a['pop'])->toBe($b['pop']);
});
