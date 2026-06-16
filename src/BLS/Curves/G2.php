<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\BLS\Curves;

use ArkEcosystem\Crypto\BLS\Fields\Fp;
use ArkEcosystem\Crypto\BLS\Fields\Fp2;

/**
 * BLS12-381 G2 curve: y² = x³ + 4(1+u) over Fp2.
 * Points in Jacobian projective coordinates (X:Y:Z) where X,Y,Z ∈ Fp2.
 * Identity = (0:1:0) (Z = Fp2::zero()).
 */
final class G2
{
    // G2 generator (from BLS12-381 spec, affine coordinates over Fp2)
    public const GX_C0 = '024aa2b2f08f0a91260805272dc51051c6e47ad4fa403b02b4510b647ae3d1770bac0326a805bbefd48056c8c121bdb8';

    public const GX_C1 = '13e02b6052719f607dacd3a088274f65596bd0d09920b61ab5da61bbdc7f5049334cf11213945d57e5ac7d055d042b7e';

    public const GY_C0 = '0ce5d527727d6e118cc9cdc6da2e351aadfd9baa8cbdd3a76d429a695160d12c923ac9cc3baca289e193548608b82801';

    public const GY_C1 = '0606c4a02ea734cc32acd2b02bc28b99cb3e287e85a763af267492ab572e99ab3f370d275cec1da1aaa9075ff05f79be';

    // BLS_X = 0xd201000000010000 (the BLS parameter for cofactor clearing)
    public const BLS_X = 'd201000000010000';

    private readonly Fp2 $x;

    private readonly Fp2 $y;

    private readonly Fp2 $z; // z = Fp2::zero() means identity

    // -------------------------------------------------------------------------
    // Frobenius (ψ) endomorphism for cofactor clearing
    // Efficient: only needs Frobenius conjugation + multiplication by PSI constants
    // -------------------------------------------------------------------------

    private static ?Fp2 $psiX  = null;

    private static ?Fp2 $psiY  = null;

    private static ?Fp2 $psi2X = null;

    public function __construct(Fp2 $x, Fp2 $y, Fp2 $z)
    {
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
    }

    public static function generator(): self
    {
        return new self(
            Fp2::fromHex(self::GX_C0, self::GX_C1),
            Fp2::fromHex(self::GY_C0, self::GY_C1),
            Fp2::one()
        );
    }

    public static function identity(): self
    {
        return new self(Fp2::zero(), Fp2::one(), Fp2::zero());
    }

    public function isIdentity(): bool
    {
        return $this->z->isZero();
    }

    // -------------------------------------------------------------------------
    // Point doubling (dbl-2009-l, a=0 for G2)
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
        $D  = $X->add($B)->square()->sub($A)->sub($C)->mulInt(2);
        $E  = $A->mulInt(3);
        $F  = $E->square();
        $X3 = $F->sub($D->mulInt(2));
        $Y3 = $E->mul($D->sub($X3))->sub($C->mulInt(8));
        $Z3 = $Y->mul($Z)->mulInt(2);

        return new self($X3, $Y3, $Z3);
    }

    // -------------------------------------------------------------------------
    // Point addition
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
        $r    = $S2->sub($S1)->mulInt(2);

        if ($H->isZero()) {
            if ($r->isZero()) {
                return $this->double();
            }

            return self::identity();
        }

        $I  = $H->mulInt(2)->square();
        $J  = $H->mul($I);
        $V  = $U1->mul($I);
        $X3 = $r->square()->sub($J)->sub($V->mulInt(2));
        $Y3 = $r->mul($V->sub($X3))->sub($S1->mul($J)->mulInt(2));
        $Z3 = $Z1->add($Z2)->square()->sub($Z1Z1)->sub($Z2Z2)->mul($H);

        return new self($X3, $Y3, $Z3);
    }

    public function neg(): self
    {
        return new self($this->x, $this->y->neg(), $this->z);
    }

    // -------------------------------------------------------------------------
    // Scalar multiplication (LSB double-and-add)
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

    /**
     * ψ (psi) Frobenius endomorphism.
     * ψ(x, y) = (conj(x) * PSI_X, conj(y) * PSI_Y)
     * Applied to projective point (X:Y:Z): ψ(X:Y:Z) = (conj(X)*PSI_X : conj(Y)*PSI_Y : conj(Z)).
     */
    public function psi(): self
    {
        if ($this->isIdentity()) {
            return self::identity();
        }
        $x2 = $this->x->conjugate()->mul(self::psiX());
        $y2 = $this->y->conjugate()->mul(self::psiY());
        $z2 = $this->z->conjugate();

        return new self($x2, $y2, $z2);
    }

    /**
     * ψ² (psi squared) endomorphism.
     * ψ²(x, y) = (x * PSI2_X, -y)
     * In projective: (X*PSI2_X : -Y : Z) — no conjugation needed since PSI2_X ∈ Fp.
     */
    public function psi2(): self
    {
        if ($this->isIdentity()) {
            return self::identity();
        }
        $x2 = $this->x->mul(self::psi2X());
        $y2 = $this->y->neg();

        return new self($x2, $y2, $this->z);
    }

    // -------------------------------------------------------------------------
    // Cofactor clearing via Bowe et al. efficient algorithm
    // h_eff = (x² - x - 1)P + (x-1)ψ(P) + ψ²(2P)
    // -------------------------------------------------------------------------

    public function clearCofactor(): self
    {
        $x = gmp_init(self::BLS_X, 16);

        $t1 = $this->scalarMul($x)->neg();  // [-x]P
        $t2 = $this->psi();                 // ψ(P)
        $t3 = $this->double();              // 2P
        $t3 = $t3->psi2();                  // ψ²(2P)
        $t3 = $t3->sub($t2);                // ψ²(2P) - ψ(P)
        $t2 = $t1->add($t2);               // [-x]P + ψ(P)
        $t2 = $t2->scalarMul($x)->neg();    // -[x]([-x]P + ψ(P)) = [x²]P - [x]ψ(P)
        $t3 = $t3->add($t2);               // ψ²(2P) - ψ(P) + [x²]P - [x]ψ(P)
        $t3 = $t3->sub($t1);               // + [x]P (sub neg = add)
        $Q  = $t3->sub($this);             // - P

        return $Q;
    }

    // sub is just add(neg)
    public function sub(self $other): self
    {
        return $this->add($other->neg());
    }

    // -------------------------------------------------------------------------
    // Convert to affine
    // -------------------------------------------------------------------------

    public function toAffine(): array  // [Fp2 $x, Fp2 $y]
    {
        if ($this->isIdentity()) {
            return [Fp2::zero(), Fp2::one()];
        }
        $zinv  = $this->z->inv();
        $zinv2 = $zinv->square();
        $zinv3 = $zinv2->mul($zinv);

        return [$this->x->mul($zinv2), $this->y->mul($zinv3)];
    }

    // -------------------------------------------------------------------------
    // Compressed serialization (ZCash encoding, 96 bytes)
    // G2 x ∈ Fp2: encode as c1 || c0 (each 48 bytes), total 96 bytes
    // -------------------------------------------------------------------------

    public function toCompressedBytes(): string
    {
        if ($this->isIdentity()) {
            $bytes    = str_repeat("\x00", 96);
            $bytes[0] = chr(0xc0); // 0x80 (compressed) | 0x40 (infinity)

            return $bytes;
        }

        [$ax, $ay] = $this->toAffine();

        // Fp2 serialization: c1 (high 48 bytes) then c0 (low 48 bytes)
        $bytes = $ax->c1->toBytes().$ax->c0->toBytes(); // 96 bytes, top 3 bits of first byte are 0

        // Bit 7 (0x80): compressed flag
        $bytes[0] = chr(ord($bytes[0]) | 0x80);

        // Bit 5 (0x20): sort/sign flag — determined by y, not x
        // ZCash: if y.c1 != 0 then sign = (y.c1 > (p-1)/2) else sign = (y.c0 > (p-1)/2)
        $signFp = $ay->c1->isZero() ? $ay->c0 : $ay->c1;
        if ($signFp->isNegative()) {
            $bytes[0] = chr(ord($bytes[0]) | 0x20);
        }

        return $bytes;
    }

    /**
     * Lazily compute and cache the three Frobenius constants.
     * base = 1/(1+u) in Fp2 = ((p+1)/2, (p-1)/2)
     * PSI_X  = base^((p-1)/3)
     * PSI_Y  = base^((p-1)/2)
     * PSI2_X = base^((p²-1)/3).
     */
    private static function initPsiConstants(): void
    {
        if (self::$psiX !== null) {
            return;
        }
        $p    = Fp::prime();
        $inv2 = gmp_div(gmp_add($p, gmp_init(1)), gmp_init(2)); // (p+1)/2 = mod-inverse of 2
        $base = new Fp2(new Fp($inv2), new Fp(gmp_sub($p, $inv2)));

        $p1         = gmp_sub($p, gmp_init(1));
        self::$psiX = $base->pow(gmp_div($p1, gmp_init(3)));
        self::$psiY = $base->pow(gmp_div($p1, gmp_init(2)));

        $p2          = gmp_mul($p, $p);
        $p2m1        = gmp_sub($p2, gmp_init(1));
        self::$psi2X = $base->pow(gmp_div($p2m1, gmp_init(3)));
    }

    private static function psiX(): Fp2
    {
        self::initPsiConstants();

        return self::$psiX;
    }

    private static function psiY(): Fp2
    {
        self::initPsiConstants();

        return self::$psiY;
    }

    private static function psi2X(): Fp2
    {
        self::initPsiConstants();

        return self::$psi2X;
    }
}
