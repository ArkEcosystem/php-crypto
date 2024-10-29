<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto\Unit\Utils;

use ArkEcosystem\Crypto\Utils\AbiDecoder;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ArkEcosystem\Crypto\Utils\AbiDecoder
 */
class AbiDecoderTest extends TestCase
{
    private AbiDecoder $decoder;

    protected function setUp(): void
    {
        $this->decoder = new AbiDecoder();
    }

    /** @test */
    public function it_should_decode_vote_payload()
    {
        $functionName        = 'vote';
        $args                = ['0x512F366D524157BcF734546eB29a6d687B762255'];
        $data                = '0x6dd7d8ea000000000000000000000000512f366d524157bcf734546eb29a6d687b762255';

        $decodedData = $this->decoder->decodeFunctionData($data);

        $this->assertSame($decodedData, [
            'functionName' => $functionName,
            'args'         => $args,
        ]);
    }
}
