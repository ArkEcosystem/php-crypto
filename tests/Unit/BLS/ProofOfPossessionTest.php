<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\BLS\EIP2333;
use ArkEcosystem\Crypto\BLS\ProofOfPossession;

const POP_SK_A_HEX   = '67d53f170b908cabb9eb326c3c337762d59289a8fec79f7bc9254b584b73265c';
const POP_SK_B_HEX   = '3325023a5e4e0069558c5bd9eb7eca78b4f4c7711b9b231d9263a8edc33bc510';
const POP_CHAIN_ID   = 10_000;
const POP_REGISTRANT = '0x75545540230d5c3BEf023202d23CB74cFA723376';
const POP_PASSPHRASE = 'peasant list dentist thrive guide uncle announce city energy artist basket divert stool glow eternal stove length gun action slice type labor aunt unlock';

const EXPECTED_PK_A         = 'a7e75af9dd4d868a41ad2f5a5b021d653e31084261724fb40ae2f1b1c31c778d3b9464502d599cf6720723ec5c68b59d';
const EXPECTED_POP_A        = 'a892e94d8ed6d0fe8792dcb31b7c5116a7d138ad4bbbd044780a7c314e86673e783850121dc34d0edfa2a2560c2f30a402f4fa5106ff71d5c69bc3027210ef90b3d3ae0a19ffc9f554b37aca72f3bb25788c3177514d94e041441ba9d029b3ba';
const EXPECTED_MNEMONIC_PK  = 'a3b93d0149c9e0ee8c2e734b641d313040b8901fcddbf61a018ae2a4633da49f9b169c0bb6653dee4cdd7dac2631a935';
const EXPECTED_MNEMONIC_POP = 'a84ec0eb9ba99033ede2bf0c64e63a4b894b4ec865c8f25c9bd44a4d11e7e2f5e0643835bdef4b983d7577dc03539b370082fa7e537b0648182296b64df8027b8d35db7f87b430f1cc1c3af733fe97862fcf2fc8aea1cc6f819b38997bbd1a6e';

// -------------------------------------------------------------------------
// buildProofOfPossession
// -------------------------------------------------------------------------

it('returns a 48-byte pk and 96-byte pop', function () {
    $result = ProofOfPossession::buildProofOfPossession(hex2bin(POP_SK_A_HEX), POP_CHAIN_ID, POP_REGISTRANT);
    // hex strings: 96 chars = 48 bytes, 192 chars = 96 bytes
    expect(strlen($result['pk']))->toBe(96)
        ->and(strlen($result['pop']))->toBe(192);
});

it('is deterministic for the same secret key', function () {
    $a = ProofOfPossession::buildProofOfPossession(hex2bin(POP_SK_A_HEX), POP_CHAIN_ID, POP_REGISTRANT);
    $b = ProofOfPossession::buildProofOfPossession(hex2bin(POP_SK_A_HEX), POP_CHAIN_ID, POP_REGISTRANT);
    expect($a['pk'])->toBe($b['pk'])
        ->and($a['pop'])->toBe($b['pop']);
});

it('produces different pk and pop for different secret keys', function () {
    $a = ProofOfPossession::buildProofOfPossession(hex2bin(POP_SK_A_HEX), POP_CHAIN_ID, POP_REGISTRANT);
    $b = ProofOfPossession::buildProofOfPossession(hex2bin(POP_SK_B_HEX), POP_CHAIN_ID, POP_REGISTRANT);
    expect($a['pk'])->not->toBe($b['pk'])
        ->and($a['pop'])->not->toBe($b['pop']);
});

it('pk matches deriveBlsPublicKey for the same secret key', function () {
    $result   = ProofOfPossession::buildProofOfPossession(hex2bin(POP_SK_A_HEX), POP_CHAIN_ID, POP_REGISTRANT);
    $expected = ProofOfPossession::privateKeyToPublicKey(hex2bin(POP_SK_A_HEX));
    expect($result['pk'])->toBe($expected);
});

it('matches the pinned test vector for SK_A', function () {
    $result = ProofOfPossession::buildProofOfPossession(hex2bin(POP_SK_A_HEX), POP_CHAIN_ID, POP_REGISTRANT);
    expect($result['pk'])->toBe(EXPECTED_PK_A)
        ->and($result['pop'])->toBe(EXPECTED_POP_A);
});

it('uses the MAINSAIL_ prefixed POP domain separation tag', function () {
    expect(ProofOfPossession::POP_DST)->toBe('MAINSAIL_BLS_POP_BLS12381G2_XMD:SHA-256_SSWU_RO_POP_');
});

it('binds the pop to the chain id and registrant address', function () {
    $bound        = ProofOfPossession::buildProofOfPossession(hex2bin(POP_SK_A_HEX), POP_CHAIN_ID, POP_REGISTRANT);
    $otherChain   = ProofOfPossession::buildProofOfPossession(hex2bin(POP_SK_A_HEX), 11_812, POP_REGISTRANT);
    $otherAddress = ProofOfPossession::buildProofOfPossession(hex2bin(POP_SK_A_HEX), POP_CHAIN_ID, '0xBd6F65c58A46427AF4B257cBE231D0eD69eD5508');

    expect($otherChain['pk'])->toBe($bound['pk'])
        ->and($otherAddress['pk'])->toBe($bound['pk'])
        ->and($otherChain['pop'])->not->toBe($bound['pop'])
        ->and($otherAddress['pop'])->not->toBe($bound['pop']);
});

it('accepts an all-lowercase registrant address', function () {
    $result = ProofOfPossession::buildProofOfPossession(hex2bin(POP_SK_A_HEX), POP_CHAIN_ID, strtolower(POP_REGISTRANT));

    expect($result['pop'])->toBe(EXPECTED_POP_A);
});

it('throws on an invalid registrant address', function () {
    expect(fn () => ProofOfPossession::buildProofOfPossession(hex2bin(POP_SK_A_HEX), POP_CHAIN_ID, '0x1234'))->toThrow(InvalidArgumentException::class, 'Invalid registrant address')
        ->and(fn () => ProofOfPossession::buildProofOfPossession(hex2bin(POP_SK_A_HEX), POP_CHAIN_ID, '0x75545540230d5c3bEf023202d23CB74cFA723376'))->toThrow(InvalidArgumentException::class, 'Invalid registrant address');
});

it('throws on a non-positive chain id', function () {
    expect(fn () => ProofOfPossession::buildProofOfPossession(hex2bin(POP_SK_A_HEX), 0, POP_REGISTRANT))->toThrow(InvalidArgumentException::class, 'Invalid chain id')
        ->and(fn () => ProofOfPossession::buildProofOfPossession(hex2bin(POP_SK_A_HEX), -1, POP_REGISTRANT))->toThrow(InvalidArgumentException::class, 'Invalid chain id');
});

it('throws on a secret key of wrong length', function () {
    expect(fn () => ProofOfPossession::buildProofOfPossession(str_repeat("\x00", 31), POP_CHAIN_ID, POP_REGISTRANT))->toThrow(InvalidArgumentException::class)
        ->and(fn () => ProofOfPossession::buildProofOfPossession(str_repeat("\x00", 33), POP_CHAIN_ID, POP_REGISTRANT))->toThrow(InvalidArgumentException::class)
        ->and(fn () => ProofOfPossession::buildProofOfPossession('', POP_CHAIN_ID, POP_REGISTRANT))->toThrow(InvalidArgumentException::class);
});

it('throws on the zero secret key', function () {
    expect(fn () => ProofOfPossession::buildProofOfPossession(str_repeat("\x00", 32), POP_CHAIN_ID, POP_REGISTRANT))->toThrow(InvalidArgumentException::class);
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
    $result = ProofOfPossession::fromMnemonic(POP_PASSPHRASE, POP_CHAIN_ID, POP_REGISTRANT);
    expect($result['pk'])->toBe(EXPECTED_MNEMONIC_PK)
        ->and($result['pop'])->toBe(EXPECTED_MNEMONIC_POP);
});

// -------------------------------------------------------------------------
// Table-driven derivation vectors
// Each entry: [mnemonic, registrant address, chain id, private key (hex), public key (hex), proof of possession (hex)]
// -------------------------------------------------------------------------

$blsDataset = [];
foreach (json_decode(file_get_contents(__DIR__.'/../../fixtures/bls-keys.json'), true) as $lang => $vectors) {
    foreach ($vectors as $i => $vector) {
        $blsDataset["{$lang} #{$i}"] = array_values($vector);
    }
}

it('derives correct public key, private key, and pop for the given mnemonic', function (string $mnemonic, string $address, int $chainId, string $expectedSk, string $expectedPk, string $expectedPop) {
    expect(bin2hex(EIP2333::deriveBlsPrivateKey($mnemonic)))->toBe($expectedSk);

    $result = ProofOfPossession::fromMnemonic($mnemonic, $chainId, $address);
    expect($result['pk'])->toBe(substr($expectedPk, 2))
        ->and($result['pop'])->toBe(substr($expectedPop, 2));
})->with($blsDataset);
