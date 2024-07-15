<?php

declare(strict_types=1);

namespace App\Crypto\EcAdapter\Impl\PhpEcc\Signature;

use App\Crypto\EcAdapter\Impl\PhpEcc\Key\XOnlyPublicKey;
use App\Crypto\EcAdapter\Impl\Secp256k1\Signature\SchnorrSignature;
use BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Adapter\EcAdapter;
use BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PrivateKey;
use BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PublicKey;
use BitWasp\Bitcoin\Exceptions\SquareRootException;
use BitWasp\Buffertools\Buffer;
use BitWasp\Buffertools\BufferInterface;
use Mdanter\Ecc\Math\NumberTheory;
use Mdanter\Ecc\Primitives\Point;
use Mdanter\Ecc\Primitives\PointInterface;

class SchnorrSigner
{
    /**
     * @var EcAdapter
     */
    private $adapter;

    public function __construct(EcAdapter $ecAdapter)
    {
        $this->adapter = $ecAdapter;
    }

    /**
     * @throws \Exception
     */
    public function sign(PrivateKey $privateKey, BufferInterface $message32): SchnorrSignature
    {
        $G = $this->adapter->getGenerator();
        $n = $G->getOrder();
        $d = $privateKey->getSecret();

        $publicKey = $privateKey->getPublicKey();

        $P = $this->getXOnlyPublicKey($publicKey);
        // $P = $privateKey->getPublicKey()->asXOnlyPublicKey();

        if (! $P->hasSquareY()) {
            $d = gmp_sub($n, $d);
        }
        $k = $this->hashPrivateData($d, $message32, $n);
        if (gmp_cmp($k, 0) === 0) {
            throw new \RuntimeException('unable to produce signature');
        }
        $R = $G->mul($k);
        if (gmp_jacobi($R->getY(), $G->getCurve()->getPrime()) !== 1) {
            $k = gmp_sub($n, $k);
        }

        $e = $this->hashPublicData($R->getX(), $P, $message32, $n);
        $s = gmp_mod(gmp_add($k, gmp_mod(gmp_mul($e, $d), $n)), $n);

        return new SchnorrSignature($R->getX(), $s);
    }

    public function getXOnlyPublicKey(PublicKey $publicKey): XOnlyPublicKey
    {
        // todo: check this, see Secp version
        $hasSquareY = gmp_cmp(gmp_jacobi($publicKey->getPoint()->getY(), $publicKey->getCurve()->getPrime()), gmp_init(1)) === 0;
        $point      = null;
        if (! $this->liftX($publicKey, $publicKey->getPoint()->getX(), $point)) {
            throw new \RuntimeException('point has no square root');
        }

        return new XOnlyPublicKey($this->adapter, $point, $hasSquareY);
    }

    public function verify(BufferInterface $msg32, XOnlyPublicKey $publicKey, SchnorrSignature $signature): bool
    {
        $G = $this->adapter->getGenerator();
        $n = $G->getOrder();
        $p = $G->getCurve()->getPrime();

        $r = $signature->getR();
        $s = $signature->getS();
        if (gmp_cmp($r, $p) >= 0 || gmp_cmp($s, $n) >= 0) {
            return false;
        }

        $RxBytes      = null;
        $e            = $this->hashPublicData($r, $publicKey, $msg32, $n, $RxBytes);
        $R            = $G->mul($s)->add($publicKey->getPoint()->mul(gmp_sub($n, $e)));
        $jacobiNotOne = gmp_jacobi($R->getY(), $p) !== 1;
        $rxNotEquals  = ! hash_equals($RxBytes, $this->tob32($R->getX()));
        if ($jacobiNotOne || $rxNotEquals) {
            return false;
        }

        return true;
    }

    private function liftX(PublicKey $publicKey, \GMP $x, ?PointInterface &$point = null): bool
    {
        $curve  = $publicKey->getCurve();
        $xCubed = gmp_powm($x, 3, $curve->getPrime());
        $v      = gmp_add($xCubed, gmp_add(
            gmp_mul($curve->getA(), $x),
            $curve->getB()
        ));
        $math = $this->adapter->getMath();
        $nt   = new NumberTheory($math);

        try {
            $y     = $nt->squareRootModP($v, $curve->getPrime());
            $point = new Point($math, $curve, $x, $y, $publicKey->getGenerator()->getOrder());

            return true;
        } catch (SquareRootException $e) {
            return false;
        }
    }

    private function tob32(\GMP $n): string
    {
        return $this->adapter->getMath()->intToFixedSizeString($n, 32);
    }

    /**
     * @throws \Exception
     */
    private function hashPrivateData(\GMP $secret, BufferInterface $message32, \GMP $n): \GMP
    {
        $hash = $this->taggedSha256('BIPSchnorrDerive', new Buffer($this->tob32($secret).$message32->getBinary()));

        return gmp_mod($hash->getGmp(), $n);
    }

    /**
     * @throws \Exception
     */
    private function hashPublicData(\GMP $Rx, XOnlyPublicKey $publicKey, BufferInterface $message32, \GMP $n, ?string &$rxBytes = null): \GMP
    {
        $rxBytes = $this->tob32($Rx);
        $hash    = $this->taggedSha256('BIPSchnorr', new Buffer($rxBytes.$publicKey->getBinary().$message32->getBinary()));

        return gmp_mod(gmp_init($hash->getHex(), 16), $n);
    }

    /**
     * Creates a tagged sha256 hash per bip-schnorr.
     *
     * @throws \Exception
     */
    private function taggedSha256(string $tag, BufferInterface $data): BufferInterface
    {
        $taghash = hash('sha256', $tag, true);
        $ctx     = hash_init('sha256');
        hash_update($ctx, $taghash);
        hash_update($ctx, $taghash);
        hash_update($ctx, $data->getBinary());

        return new Buffer(hash_final($ctx, true));
    }
}
