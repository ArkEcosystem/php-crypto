<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\EcAdapter\Impl\Secp256k1\Signature;

interface SchnorrSignatureInterface
{
    public function getR(): \GMP;

    public function getS(): \GMP;
}
