<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\BLS\Fields;

use GMP;

/**
 * Fp2 = Fp[u] / (u² + 1) — the quadratic extension field for BLS12-381 G2.
 * An element is (c0 + c1·u) where c0, c1 ∈ Fp.
 */
final class Fp2
{
    public readonly Fp $c0;

    public readonly Fp $c1;

    public function __construct(Fp $c0, Fp $c1)
    {
        $this->c0 = $c0;
        $this->c1 = $c1;
    }

    // -------------------------------------------------------------------------
    // Constructors
    // -------------------------------------------------------------------------

    public static function fromInts(int $c0, int $c1): self
    {
        return new self(Fp::fromInt($c0), Fp::fromInt($c1));
    }

    public static function fromHex(string $c0hex, string $c1hex): self
    {
        return new self(Fp::fromHex($c0hex), Fp::fromHex($c1hex));
    }

    public static function zero(): self
    {
        return new self(Fp::zero(), Fp::zero());
    }

    public static function one(): self
    {
        return new self(Fp::one(), Fp::zero());
    }

    // -------------------------------------------------------------------------
    // Arithmetic
    // -------------------------------------------------------------------------

    public function add(self $other): self
    {
        return new self($this->c0->add($other->c0), $this->c1->add($other->c1));
    }

    public function sub(self $other): self
    {
        return new self($this->c0->sub($other->c0), $this->c1->sub($other->c1));
    }

    /**
     * Karatsuba multiplication in Fp2.
     * (a + b·u)(c + d·u) = (ac - bd) + (ad + bc)·u  [since u²=-1].
     */
    public function mul(self $other): self
    {
        $ac = $this->c0->mul($other->c0);
        $bd = $this->c1->mul($other->c1);
        $c0 = $ac->sub($bd);
        // (a+b)(c+d) - ac - bd = ad + bc
        $c1 = $this->c0->add($this->c1)->mul($other->c0->add($other->c1))->sub($ac)->sub($bd);

        return new self($c0, $c1);
    }

    public function mulFp(Fp $scalar): self
    {
        return new self($this->c0->mul($scalar), $this->c1->mul($scalar));
    }

    public function mulInt(int $n): self
    {
        $s = Fp::fromInt($n);

        return new self($this->c0->mul($s), $this->c1->mul($s));
    }

    public function square(): self
    {
        // (a + b·u)² = (a²-b²) + 2ab·u
        $a  = $this->c0;
        $b  = $this->c1;
        $c0 = $a->add($b)->mul($a->sub($b)); // (a+b)(a-b) = a²-b²
        $c1 = $a->mul($b)->mul(Fp::fromInt(2));

        return new self($c0, $c1);
    }

    public function neg(): self
    {
        return new self($this->c0->neg(), $this->c1->neg());
    }

    /** Frobenius / conjugate: (a + b·u)* = a - b·u */
    public function conjugate(): self
    {
        return new self($this->c0, $this->c1->neg());
    }

    public function inv(): self
    {
        // 1/(a + b·u) = (a - b·u) / (a² + b²)
        $norm    = $this->c0->square()->add($this->c1->square()); // a² + b²
        $invNorm = $norm->inv();

        return new self($this->c0->mul($invNorm), $this->c1->neg()->mul($invNorm));
    }

    public function div(self $other): self
    {
        return $this->mul($other->inv());
    }

    /**
     * Exponentiation by a large GMP integer.
     */
    public function pow(GMP $exp): self
    {
        $result = self::one();
        $base   = $this;
        $e      = gmp_abs($exp);
        while (gmp_cmp($e, gmp_init(0)) > 0) {
            if (gmp_testbit($e, 0)) {
                $result = $result->mul($base);
            }
            $base = $base->square();
            $e    = gmp_div($e, gmp_init(2));
        }

        return $result;
    }

    // -------------------------------------------------------------------------
    // Predicates
    // -------------------------------------------------------------------------

    public function isZero(): bool
    {
        return $this->c0->isZero() && $this->c1->isZero();
    }

    public function equals(self $other): bool
    {
        return $this->c0->equals($other->c0) && $this->c1->equals($other->c1);
    }

    /**
     * sgn0_m_eq_2 from RFC 9380: sign of Fp2 element.
     * Returns c0's parity unless c0==0, then c1's parity.
     */
    public function isOdd(): bool
    {
        if (! $this->c0->isZero()) {
            return $this->c0->isOdd();
        }

        return $this->c1->isOdd();
    }

    public static function cmov(self $a, self $b, bool $condition): self
    {
        return $condition ? $b : $a;
    }

    // -------------------------------------------------------------------------
    // Serialization (96 bytes: c1 first 48 bytes, c0 last 48 bytes)
    // G2 compressed: 96 bytes per coordinate pair, two pairs = 192 bytes total
    // -------------------------------------------------------------------------

    public function toBytes(): string
    {
        return $this->c1->toBytes().$this->c0->toBytes();
    }
}
