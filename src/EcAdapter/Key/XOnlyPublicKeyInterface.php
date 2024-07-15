<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\EcAdapter\Key;

use ArkEcosystem\Crypto\EcAdapter\Impl\Secp256k1\Signature\SchnorrSignatureInterface;
use BitWasp\Bitcoin\SerializableInterface;
use BitWasp\Buffertools\BufferInterface;

interface XOnlyPublicKeyInterface extends SerializableInterface
{
    public function hasSquareY(): bool;

    public function verifySchnorr(BufferInterface $msg32, SchnorrSignatureInterface $schnorrSig): bool;

    public function tweakAdd(BufferInterface $tweak32): self;

    public function checkPayToContract(self $base, BufferInterface $hash, bool $negated): bool;

    public function getBuffer(): BufferInterface;
}
