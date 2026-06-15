<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\Utils\RlpDecoder;

it('should_decode_function_call', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'transfer');

    // Remove '0x' prefix from serialized hex string
    $serialized = $fixture['serialized'];

    $decoded_rlp = RlpDecoder::decode('0x'.$serialized);

    expect(count($decoded_rlp))->toBe(9);
    expect($decoded_rlp[0])->toBe('0x01');
    expect($decoded_rlp[1])->toBe('0x012a05f200');
    expect($decoded_rlp[2])->toBe('0x5208');
    expect($decoded_rlp[3])->toBe('0x6f0182a0cc707b055322ccf6d4cb6a5aff1aeb22');
    expect($decoded_rlp[4])->toBe('0x05f5e100');
    expect($decoded_rlp[5])->toBe('0x');
    expect($decoded_rlp[6])->toBe('0x5c6b'); // Chain ID * 2 + 35
    expect($decoded_rlp[7])->toBe('0xa1f79cb40a4bb409d6cebd874002ceda3ec0ccb614c1d8155f5c2f7f798135f9');
    expect($decoded_rlp[8])->toBe('0x2d2ef517aaf6feed747385e260c206f46b2ce9d6b2a585427a111685a097bd79');
});

it('should_decoding_str', function () {
    $decoded = RlpDecoder::decode('0x8774657374696e67');

    expect($decoded)->toBe('0x74657374696e67');
});

it('should_decoding_bytes', function () {
    $decoded = RlpDecoder::decode('0x8774657374696e67');

    expect($decoded)->toBe('0x74657374696e67');
});

it('should_decoding_list', function () {
    $decoded = RlpDecoder::decode('0xc88774657374696e67');

    expect($decoded)->toBe(['0x74657374696e67']);
});

it('should_decoding_int', function () {
    $decoded = RlpDecoder::decode('0x86313233343536');

    expect($decoded)->toBe('0x313233343536');
});

it('should error with unexpected junk at the end', function () {
    RlpDecoder::decode('0xc88774657374696e67ff');
})->throws(InvalidArgumentException::class, 'unexpected junk after RLP payload');

it('should decode hex value', function () {
    $decoded = RlpDecoder::decode('0x821212');

    expect($decoded)->toBe('0x1212');
});

it('should handle empty value', function () {
    RlpDecoder::decode('0x');
})->throws(InvalidArgumentException::class, 'RLP data is empty');

it('should handle invalid bytes-like value', function () {
    RlpDecoder::decode('invalid');
})->throws(InvalidArgumentException::class, 'Invalid BytesLike value for "data": invalid');

it('should handle out of range data', function () {
    RlpDecoder::decode('0xc8');
})->throws(InvalidArgumentException::class, 'data short segment or out of range');

it('should handle malformed child data', function () {
    RlpDecoder::decode('0xc3018200');
})->throws(InvalidArgumentException::class, 'child data too short or malformed');

it('should decode array values', function () {
    $decoded = RlpDecoder::decode('0xf85b8774657374696e678c616e6f7468657220746573748974657374696e6720328974657374696e6720338974657374696e6720348974657374696e6720358974657374696e6720368974657374696e6720378974657374696e672038');

    expect($decoded)->toBe([
        '0x74657374696e67',
        '0x616e6f746865722074657374',
        '0x74657374696e672032',
        '0x74657374696e672033',
        '0x74657374696e672034',
        '0x74657374696e672035',
        '0x74657374696e672036',
        '0x74657374696e672037',
        '0x74657374696e672038',
    ]);
});

it('should decode large values', function () {
    $decoded = RlpDecoder::decode('0xf852b8503132333435363738393031323334353637383930313233343536373839303132333435363738393031323334353637383930313233343536373839303132333435363738393031323334353637383930');

    expect($decoded)->toBe(['0x3132333435363738393031323334353637383930313233343536373839303132333435363738393031323334353637383930313233343536373839303132333435363738393031323334353637383930']);
});
