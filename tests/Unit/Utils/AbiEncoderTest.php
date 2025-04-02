<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\Utils\AbiEncoder;

beforeEach(function () {
    $this->encoder = new AbiEncoder();
});

test('it should encode vote function call', function () {
    $functionName        = 'vote';
    $args                = ['0x512F366D524157BcF734546eB29a6d687B762255'];
    $expectedEncodedData = '0x6dd7d8ea000000000000000000000000512f366d524157bcf734546eb29a6d687b762255';

    $encodedData = $this->encoder->encodeFunctionCall($functionName, $args);

    expect($encodedData)->toBe($expectedEncodedData);
});
