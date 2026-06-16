<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\BLS\EIP2333;

const EIP2333_MNEMONIC     = 'peasant list dentist thrive guide uncle announce city energy artist basket divert stool glow eternal stove length gun action slice type labor aunt unlock';
const EIP2333_EXPECTED_SK  = '1e8241be151e557aa1de77c3732cd256461d5a1b946e044e3119efbd14e68e45';

it('derives the correct private key from the canonical mnemonic', function () {
    $sk = EIP2333::deriveBlsPrivateKey(EIP2333_MNEMONIC);
    expect(bin2hex($sk))->toBe(EIP2333_EXPECTED_SK);
});

it('produces different private keys for different mnemonics', function () {
    $a = EIP2333::deriveBlsPrivateKey(EIP2333_MNEMONIC);
    $b = EIP2333::deriveBlsPrivateKey('zoo zoo zoo zoo zoo zoo zoo zoo zoo zoo zoo wrong');
    expect(bin2hex($a))->not->toBe(bin2hex($b));
});
