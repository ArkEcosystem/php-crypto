<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\BLS\Curves;

use ArkEcosystem\Crypto\BLS\Fields\Fp;

/**
 * BLS12-381 G1 curve: y² = x³ + 4 over Fp.
 * Points stored in Jacobian projective coordinates (X:Y:Z), affine = (X/Z², Y/Z³).
 * Identity = (0:1:0).
 */
final class G1
{
    // Generator point (affine)
    const GX = '17f1d3a73197d7942695638c4fa9ac0fc3688c4f9774b905a14e3a3f171bac586c55e83ff97a1aeffb3af00adb22c6bb';
    const GY = '08b3f481e3aaa0f1a09e30ed741d8ae4fcf5e095d5d00af600db18cb2c04b3edd03cc744a2888ae40caa232946c5e7e1';

    private readonly Fp $x;
    private readonly Fp $y;
    private readonly Fp $z; // z = zero means identity

    private function __construct(Fp $x, Fp $y, Fp $z)
    {
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
    }

    public static function generator(): self
    {
        return new self(Fp::fromHex(self::GX), Fp::fromHex(self::GY), Fp::one());
    }

    public static function identity(): self
    {
        return new self(Fp::zero(), Fp::one(), Fp::zero());
    }

    public function isIdentity(): bool
    {
        return $this->z->isZero();
    }

    // -------------------------------------------------------------------------
    // Point doubling (dbl-2009-l, a=0)
    // -------------------------------------------------------------------------

    public function double(): self
    {
        if ($this->isIdentity()) {
            return self::identity();
        }

        $X = $this->x;
        $Y = $this->y;
        $Z = $this->z;

        $A  = $X->square();
        $B  = $Y->square();
        $C  = $B->square();
        $D  = $X->add($B)->square()->sub($A)->sub($C)->mul(Fp::fromInt(2));
        $E  = $A->mul(Fp::fromInt(3));
        $F  = $E->square();
        $X3 = $F->sub($D->mul(Fp::fromInt(2)));
        $Y3 = $E->mul($D->sub($X3))->sub($C->mul(Fp::fromInt(8)));
        $Z3 = $Y->mul($Z)->mul(Fp::fromInt(2));

        return new self($X3, $Y3, $Z3);
    }

    // -------------------------------------------------------------------------
    // Point addition (add-2007-bl, handles mixed/projective)
    // -------------------------------------------------------------------------

    public function add(self $other): self
    {
        if ($this->isIdentity()) {
            return $other;
        }
        if ($other->isIdentity()) {
            return $this;
        }

        $X1 = $this->x;
        $Y1 = $this->y;
        $Z1 = $this->z;
        $X2 = $other->x;
        $Y2 = $other->y;
        $Z2 = $other->z;

        $Z1Z1 = $Z1->square();
        $Z2Z2 = $Z2->square();
        $U1   = $X1->mul($Z2Z2);
        $U2   = $X2->mul($Z1Z1);
        $S1   = $Y1->mul($Z2)->mul($Z2Z2);
        $S2   = $Y2->mul($Z1)->mul($Z1Z1);
        $H    = $U2->sub($U1);
        $r    = $S2->sub($S1)->mul(Fp::fromInt(2));

        // If H == 0 and r == 0: both points are equal, use double
        if ($H->isZero()) {
            if ($r->isZero()) {
                return $this->double();
            }
            return self::identity(); // opposite points
        }

        $I  = $H->mul(Fp::fromInt(2))->square();
        $J  = $H->mul($I);
        $V  = $U1->mul($I);
        $X3 = $r->square()->sub($J)->sub($V->mul(Fp::fromInt(2)));
        $Y3 = $r->mul($V->sub($X3))->sub($S1->mul($J)->mul(Fp::fromInt(2)));
        $Z3 = $Z1->add($Z2)->square()->sub($Z1Z1)->sub($Z2Z2)->mul($H);

        return new self($X3, $Y3, $Z3);
    }

    public function neg(): self
    {
        return new self($this->x, $this->y->neg(), $this->z);
    }

    // -------------------------------------------------------------------------
    // Scalar multiplication (double-and-add from MSB)
    // -------------------------------------------------------------------------

    public function scalarMul(\GMP $k): self
    {
        if (gmp_sign($k) === 0) {
            return self::identity();
        }

        $result = self::identity();
        $addend = $this;
        $n      = gmp_abs($k);

        while (gmp_cmp($n, gmp_init(0)) > 0) {
            if (gmp_testbit($n, 0)) {
                $result = $result->add($addend);
            }
            $addend = $addend->double();
            $n      = gmp_div($n, gmp_init(2));
        }

        if (gmp_sign($k) < 0) {
            return $result->neg();
        }

        return $result;
    }

    // -------------------------------------------------------------------------
    // Compressed serialization (ZCash encoding, 48 bytes)
    // -------------------------------------------------------------------------

    public function toCompressedBytes(): string
    {
        if ($this->isIdentity()) {
            $bytes    = str_repeat("\x00", 48);
            $bytes[0] = chr(0xc0); // 0x80 (compressed) | 0x40 (infinity)
            return $bytes;
        }

        // Convert to affine
        $zinv  = $this->z->inv();
        $zinv2 = $zinv->square();
        $zinv3 = $zinv2->mul($zinv);
        $ax    = $this->x->mul($zinv2);
        $ay    = $this->y->mul($zinv3);

        $bytes = $ax->toBytes(); // 48 bytes, top 3 bits of first byte are 0
        // Bit 7 (0x80): compressed flag
        $bytes[0] = chr(ord($bytes[0]) | 0x80);
        // Bit 5 (0x20): sort/sign flag, set if y > (p-1)/2
        if ($ay->isNegative()) {
            $bytes[0] = chr(ord($bytes[0]) | 0x20);
        }

        return $bytes;
    }

    public function toHex(): string
    {
        return bin2hex($this->toCompressedBytes());
    }
}
