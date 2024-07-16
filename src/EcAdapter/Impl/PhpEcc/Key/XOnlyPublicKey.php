<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\EcAdapter\Impl\PhpEcc\Key;

use ArkEcosystem\Crypto\EcAdapter\Impl\PhpEcc\Signature\SchnorrSigner;
use ArkEcosystem\Crypto\EcAdapter\Impl\Secp256k1\Signature\SchnorrSignatureInterface;
use ArkEcosystem\Crypto\EcAdapter\Key\XOnlyPublicKeyInterface;
use BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Adapter\EcAdapter;
use BitWasp\Bitcoin\Serializable;
use BitWasp\Buffertools\Buffer;
use BitWasp\Buffertools\BufferInterface;
use Mdanter\Ecc\Primitives\PointInterface;

class XOnlyPublicKey extends Serializable implements XOnlyPublicKeyInterface
{
    private $point;

    private $adapter;

    private $hasSquareY;

    public function __construct(EcAdapter $adapter, PointInterface $point, bool $hasSquareY)
    {
        $this->adapter    = $adapter;
        $this->point      = $point;
        $this->hasSquareY = $hasSquareY;
    }

    public function hasSquareY(): bool
    {
        return $this->hasSquareY;
    }

    public function getPoint(): PointInterface
    {
        return $this->point;
    }

    public function verifySchnorr(BufferInterface $msg32, SchnorrSignatureInterface $schnorrSig): bool
    {
        $schnorr = new SchnorrSigner($this->adapter);

        return $schnorr->verify($msg32, $this, $schnorrSig);
    }

    public function tweakAdd(BufferInterface $tweak32): XOnlyPublicKeyInterface
    {
        $G        = $this->adapter->getGenerator();
        $curve    = $G->getCurve();
        $n        = $G->getOrder();
        $gmpTweak = $tweak32->getGmp();
        if (gmp_cmp($gmpTweak, $n) >= 0) {
            throw new \RuntimeException('invalid tweak');
        }
        $offset   = $this->adapter->getGenerator()->mul($gmpTweak);
        $newPoint = $this->point->add($offset);
        // todo: check this out
        $hasSquareY = gmp_cmp(gmp_jacobi($newPoint->getY(), $curve->getPrime()), gmp_init(1)) === 0;

        return new self($this->adapter, $newPoint, $hasSquareY);
    }

    public function checkPayToContract(XOnlyPublicKeyInterface $base, BufferInterface $hash, bool $negated): bool
    {
        return $this->tweakTest($base, $hash, ! $negated);
    }

    public function getBuffer(): BufferInterface
    {
        return Buffer::int(gmp_strval($this->point->getX(), 10), 32);
    }

    private function tweakTest(XOnlyPublicKeyInterface $base, BufferInterface $hash, bool $hasSquareY): bool
    {
        $pkExpected   = $base->tweakAdd($hash);
        $xEquals      = gmp_cmp($pkExpected->getPoint()->getX(), $this->point->getX()) === 0;
        $squareEquals = $pkExpected->hasSquareY() === $hasSquareY;

        /** @var XOnlyPublicKey $pkExpected */
        return $xEquals && $squareEquals;
    }
}