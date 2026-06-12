<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\BLS\EIP2333;
use ArkEcosystem\Crypto\BLS\ProofOfPossession;

const POP_SK_A_HEX   = '67d53f170b908cabb9eb326c3c337762d59289a8fec79f7bc9254b584b73265c';
const POP_SK_B_HEX   = '3325023a5e4e0069558c5bd9eb7eca78b4f4c7711b9b231d9263a8edc33bc510';
const POP_PASSPHRASE = 'peasant list dentist thrive guide uncle announce city energy artist basket divert stool glow eternal stove length gun action slice type labor aunt unlock';

const EXPECTED_PK_A         = 'a7e75af9dd4d868a41ad2f5a5b021d653e31084261724fb40ae2f1b1c31c778d3b9464502d599cf6720723ec5c68b59d';
const EXPECTED_POP_A        = '878ad02e1f215d40722bd77a0148adb8dfaad4514157600a0a926cfc58589fa4e79d3d4d579cc4149237b8100efdcff110dd2a251c52543539d499c8f24b142da66d1dc19ec44b3d9c3f71112b2705e5557f932a36bd9cd9b3544ab0d9e6a677';
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
    expect(fn () => ProofOfPossession::buildProofOfPossession(str_repeat("\x00", 31)))->toThrow(InvalidArgumentException::class)
        ->and(fn () => ProofOfPossession::buildProofOfPossession(str_repeat("\x00", 33)))->toThrow(InvalidArgumentException::class)
        ->and(fn () => ProofOfPossession::buildProofOfPossession(''))->toThrow(InvalidArgumentException::class);
});

it('throws on the zero secret key', function () {
    expect(fn () => ProofOfPossession::buildProofOfPossession(str_repeat("\x00", 32)))->toThrow(InvalidArgumentException::class);
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

// -------------------------------------------------------------------------
// Table-driven derivation vectors
// Add new rows as mnemonics and expected values are verified.
// [mnemonic, private key, public key, proof of possession]
// -------------------------------------------------------------------------

$testData = <<<'JSON'
    {
      "English": [
        "scrap math switch nominee eyebrow stone melody first episode focus piece weekend amount spirit novel warfare special soda stone become sad oven example cannon",
        "38b19fd74675ebab3063162f695423887ca3c1408561d3ed426c36bae5c19652",
        "a611f22e481a213eef530b550f1e16ba1c4ac2263088a7b0c167c0ecc62328926581bbc50c36445a44bc8af9720ae90c",
        "b1666a77167332a077b93e7e54ef1b83776c43f25d4f59dabec1a240dee28c8ae88f0146643b9a8ba8493c43d8390892019fbe134742cec235dd99ebad514c508031b61db88c6143705cde6c1dfe68e52f21f0f7eb400576703ab006acea1631"
      ],
      "Chinese Simplified": [
        "逻 砖 浇 动 牌 霞 扎 团 柴 年 虽 类 因 什 电 骂 后 什 帽 玻 缩 像 壮 摘",
        "3c91a0142bdb0c777b64c74ae2a76cb110b1cdd56f2781aeb5ba7f1eac983b91",
        "a6fab215d09829188d9fe753ae0162cb0aacfc875a2246550585205cd95e062b79585402b3ef853a8afc73a13e09065b",
        "b2333783b03f6b0c095391c48b874a1d4e6beac5c840a5a89ef3954f94bf5519210578aa734ecbe90e90a7417eed001a01e3402e3c899183ffee6fe636a02833276b0f3bac543312804d2c9908233252e998696d6fd34df847137b48c4548b78"
      ],
      "Chinese Traditional": [
        "溶 班 顧 政 候 頭 艱 應 麥 腔 鴨 崇 鳥 飲 器 遼 避 吏 相 帝 牢 題 再 便",
        "330222326a53a94f3c4d916b41e0bfc89e18c2276dba798b5ecefefba2375e0f",
        "a40fe8429f7bbca2f3c013c1297c906fc4200272b58bc45e3c7fbe3806a83d2b51ac148c7a4a56bd5d8e41cf38f91c7c",
        "80cdad2f7756f1ac6da37767a1b36e0395db9971bd9005a5f52915c2d18aaeae648a84688c42b28f7c00973c91b9a1d91600a6359346309ac05330bc127d14bc7cbc6a9d655b4d0c2745dcd3adc7f05301d8473ecb2388ed008c922fd6ad64bc"
      ],
      "French": [
        "musicien vecteur gorille carnage toboggan lavoir zoologie hiberner bavarder dégager amovible glace plaque sauter cloche griffure assiette fraise éjecter enduire chance sceptre ronce formuler",
        "1a3ed283df0f998e2d7295c147bb184f2ef37b857eb307bd2328cbdca9782732",
        "a600ae9122d27e4b1665b5b7191288295bac34cd4c64198ba5e3e08016fd5b9391dc4a7e18e3044dd0482fe6b09cf492",
        "805e9c3c7e0fb4c89f90565a95fbf9b5d73e5c4ae1ae2f8d5ef924abb5fa77845b631827ffc517b219cdc95638a61bba0c09f2f49fce0867f68dee93b8b9ab127e5e32521bc6629a81ac859f5d5b5acfff7253833fe3d849e48cb3bf0809eb3c"
      ],
      "Italian": [
        "capello tendone forzare satellite fuso spessore treccia benzina scindere molosso lancetta tortora sposo impeto illeso daino torrone pollice chela ocra bilancia ripetuto totano energia",
        "1453e74fd4f04e0d8eb5b140b773fdaaf0e6e71f2d3518dd186a16cba64e0fc7",
        "b22f2f96b7e4dada84f57c63bd8b5b2c65279e3fcb734833eceb6c19a5f942f354fb0aad0d9bee9b444a0f23493b5a02",
        "a35092f3132b0ee9d08afd082866f9c231ae09b41431ad715d3ac0afa06bdb603f026cd1aec872b5a3cbf5ff0aa9e0e102e69c77c75849969fdd03da81bfb9f1385c62837414a6770c94e5a92a629710dba127597fb1f1bf7dcd675563913635"
      ],
      "Japanese": [
        "ひさしぶり　そえもの　さんか　なれる　つみき　はつおん　いさん　おかわり　そっこう　にんめい　おうたい　すばらしい　さよう　えんぜつ　ひびく　のはら　ときどき　ろんぱ　さのう　いんげんまめ　きりん　むしろ　なみだ　おやゆび",
        "2770417db1d1e16f183a033df92f182e680621cccb11c9fd094c663c9f884a4d",
        "a9579cd51bf4cde66a02fe8c063b3e7b24940ed73542e22dd20598a5bc32a521adfa38fe42ea290a335a038d645a8fa4",
        "8b0da67bb4b679cd6922848da1fcdf40e7624302b61bdcec6184d7ab05220921bf6aaa1deb6ab513eebedccdaf25221a05d0bf21672ff6802734ad366c18bca2f1a97ac3bfdac17213d04900d7bb6acb9d62d7be7070c001083229b6727767d6"
      ],
      "Korean": [
        "한마디 티셔츠 가죽 반지 상관 예산 주름 대규모 추억 시나리오 생선 대략 용서 구석 유학 원피스 통제 활동 술집 학교 비명 유학 이윽고 믿음",
        "01736f1b05ab33e5699c7c410a53929f961a2f78eb7f64acad44d3ef08b2780d",
        "a17e76cb9eb3557185830779de5d3575a4b6cb0ed9d6ecdf93bc221a45b916b87ccf644552820233845e44e898afd054",
        "967850f2d81330742665d7b8a6d8ccf83363c56be5b278c79301baf1eb61e916cebe84c47188236333db5b8e1da196bd0df37a7da3b91cf6f6e38ee218172721f63308c0eaa80922d7ee124eede1975cb5dd6ce3f4742625e444f92789407591"
      ],
      "Spanish": [
        "sábado droga academia corazón sexto diadema tórax lavar silencio agonía figura dolor llover lámina divino coger lucir escala feroz ópera firma medio seis renta",
        "31a9f393c76630de49fc24b160ac2312dd0d95a7bea4c325fca835161c0b918b",
        "a92746694fdf3248493d292ba67e802c4076e6c0c62f8137b81c5df4c8c7c67ef7bda33fe7a2ae033d6c2a642f412cba",
        "b4a42854f004c3e7e9c0ea82ba5972f2afdd40a1adc4e2b94692db0a3ae8763091da7ce422ed60cf520de611b4ed361a0ad97220dc3c23b6c4f544559362eb077729331842401c8e225367ae5bca34ce5cb1bf1933ae9b7de3ac64b7465af1c6"
      ]
    }
JSON;

it('derives correct public key, private key, and pop for the given mnemonic', function (string $mnemonic, string $expectedSk, string $expectedPk, string $expectedPop) {
    expect(bin2hex(EIP2333::deriveBlsPrivateKey($mnemonic)))->toBe($expectedSk);

    $result = ProofOfPossession::fromMnemonic($mnemonic);
    expect($result['pk'])->toBe($expectedPk)
        ->and($result['pop'])->toBe($expectedPop);
})->with(json_decode($testData, true));
