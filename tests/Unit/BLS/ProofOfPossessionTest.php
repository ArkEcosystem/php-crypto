<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\BLS\ProofOfPossession;

const POP_SK_A_HEX   = '67d53f170b908cabb9eb326c3c337762d59289a8fec79f7bc9254b584b73265c';
const POP_SK_B_HEX   = '3325023a5e4e0069558c5bd9eb7eca78b4f4c7711b9b231d9263a8edc33bc510';
const POP_PASSPHRASE = 'peasant list dentist thrive guide uncle announce city energy artist basket divert stool glow eternal stove length gun action slice type labor aunt unlock';

const EXPECTED_PK_A  = 'a7e75af9dd4d868a41ad2f5a5b021d653e31084261724fb40ae2f1b1c31c778d3b9464502d599cf6720723ec5c68b59d';
const EXPECTED_POP_A = '878ad02e1f215d40722bd77a0148adb8dfaad4514157600a0a926cfc58589fa4e79d3d4d579cc4149237b8100efdcff110dd2a251c52543539d499c8f24b142da66d1dc19ec44b3d9c3f71112b2705e5557f932a36bd9cd9b3544ab0d9e6a677';
const EXPECTED_MNEMONIC_PK  = 'a3b93d0149c9e0ee8c2e734b641d313040b8901fcddbf61a018ae2a4633da49f9b169c0bb6653dee4cdd7dac2631a935';
const EXPECTED_MNEMONIC_POP = 'b72a4b4601608029564c691c742bac7a089c355e9e4e3c60469bc429ca24a24f1a568377df210755c307939065ae8954125c823fea74ee3fe5fa9ecd5dfd4a33a540d1835acb086b968ca7b543d3258bb2afb7911fb1d2e560004d6e280c043c';

// -------------------------------------------------------------------------
// buildProofOfPossession
// -------------------------------------------------------------------------

it('returns a 48-byte pk and 96-byte pop', function () {
    $result = ProofOfPossession::buildProofOfPossession(hex2bin(POP_SK_A_HEX));
    // hex strings: 96 chars = 48 bytes, 192 chars = 96 bytes
    expect(strlen($result['pk']))->toBe(96)
        ->and(strlen($result['pop']))->toBe(192);
});

it('is deterministic for the same secret key', function () {
    $a = ProofOfPossession::buildProofOfPossession(hex2bin(POP_SK_A_HEX));
    $b = ProofOfPossession::buildProofOfPossession(hex2bin(POP_SK_A_HEX));
    expect($a['pk'])->toBe($b['pk'])
        ->and($a['pop'])->toBe($b['pop']);
});

it('produces different pk and pop for different secret keys', function () {
    $a = ProofOfPossession::buildProofOfPossession(hex2bin(POP_SK_A_HEX));
    $b = ProofOfPossession::buildProofOfPossession(hex2bin(POP_SK_B_HEX));
    expect($a['pk'])->not->toBe($b['pk'])
        ->and($a['pop'])->not->toBe($b['pop']);
});

it('pk matches deriveBlsPublicKey for the same secret key', function () {
    $result   = ProofOfPossession::buildProofOfPossession(hex2bin(POP_SK_A_HEX));
    $expected = ProofOfPossession::privateKeyToPublicKey(hex2bin(POP_SK_A_HEX));
    expect($result['pk'])->toBe($expected);
});

it('matches the pinned test vector for SK_A', function () {
    $result = ProofOfPossession::buildProofOfPossession(hex2bin(POP_SK_A_HEX));
    expect($result['pk'])->toBe(EXPECTED_PK_A)
        ->and($result['pop'])->toBe(EXPECTED_POP_A);
});

it('throws on a secret key of wrong length', function () {
    expect(fn() => ProofOfPossession::buildProofOfPossession(str_repeat("\x00", 31)))->toThrow(\InvalidArgumentException::class)
        ->and(fn() => ProofOfPossession::buildProofOfPossession(str_repeat("\x00", 33)))->toThrow(\InvalidArgumentException::class)
        ->and(fn() => ProofOfPossession::buildProofOfPossession(''))->toThrow(\InvalidArgumentException::class);
});

it('throws on the zero secret key', function () {
    expect(fn() => ProofOfPossession::buildProofOfPossession(str_repeat("\x00", 32)))->toThrow(\InvalidArgumentException::class);
});

// -------------------------------------------------------------------------
// deriveBlsPublicKey
// -------------------------------------------------------------------------

it('returns a 96-character hex string (48-byte G1)', function () {
    $pk = ProofOfPossession::deriveBlsPublicKey(POP_PASSPHRASE);
    expect(strlen($pk))->toBe(96);
});

it('is deterministic for the same passphrase', function () {
    $pk = ProofOfPossession::deriveBlsPublicKey(POP_PASSPHRASE);
    expect($pk)->toBe(EXPECTED_MNEMONIC_PK);
});

it('matches the pinned pop for the same passphrase', function () {
    $result = ProofOfPossession::fromMnemonic(POP_PASSPHRASE);
    expect($result['pk'])->toBe(EXPECTED_MNEMONIC_PK)
        ->and($result['pop'])->toBe(EXPECTED_MNEMONIC_POP);
});
