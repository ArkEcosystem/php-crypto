<?php

declare(strict_types=1);

namespace App\Crypto\EcAdapter\Impl\Secp256k1\Signature;

interface SchnorrSignatureInterface
{
    public function getR(): \GMP;

    public function getS(): \GMP;
}
