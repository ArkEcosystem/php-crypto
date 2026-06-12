<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\BLS\Fields;

use GMP;

/**
 * BLS12-381 base field Fp = Z/pZ where p is the BLS12-381 field prime.
 * All arithmetic is backed by ext-gmp.
 */
final class Fp
{
    // BLS12-381 field prime
    public const P_HEX = '1a0111ea397fe69a4b1ba7b6434bacd764774b84f38512bf6730d2a0f6b0f6241eabfffeb153ffffb9feffffffffaaab';

    // Subgroup order r (used by EIP-2333 hkdfModR)
    public const R_HEX = '73eda753299d7d483339d80809a1d80553bda402fffe5bfeffffffff00000001';

    public readonly GMP $value;

    private static ?GMP $prime = null;

    private static ?GMP $order = null;

    public function __construct(GMP $value)
    {
        $p = self::prime();
        $v = gmp_mod($value, $p);
        // gmp_mod always returns non-negative when $p > 0
        $this->value = $v;
    }

    // -------------------------------------------------------------------------
    // Constructors
    // -------------------------------------------------------------------------

    public static function fromHex(string $hex): self
    {
        return new self(gmp_init($hex, 16));
    }

    public static function fromInt(int $n): self
    {
        return new self(gmp_init($n));
    }

    public static function zero(): self
    {
        return new self(gmp_init(0));
    }

    public static function one(): self
    {
        return new self(gmp_init(1));
    }

    public static function prime(): GMP
    {
        if (self::$prime === null) {
            self::$prime = gmp_init(self::P_HEX, 16);
        }

        return self::$prime;
    }

    public static function order(): GMP
    {
        if (self::$order === null) {
            self::$order = gmp_init(self::R_HEX, 16);
        }

        return self::$order;
    }

    // -------------------------------------------------------------------------
    // Arithmetic
    // -------------------------------------------------------------------------

    public function add(self $other): self
    {
        return new self(gmp_add($this->value, $other->value));
    }

    public function sub(self $other): self
    {
        $r = gmp_sub($this->value, $other->value);
        if (gmp_sign($r) < 0) {
            $r = gmp_add($r, self::prime());
        }

        return new self($r);
    }

    public function mul(self $other): self
    {
        return new self(gmp_mul($this->value, $other->value));
    }

    public function square(): self
    {
        return new self(gmp_pow($this->value, 2));
    }

    public function neg(): self
    {
        if (gmp_sign($this->value) === 0) {
            return self::zero();
        }

        return new self(gmp_sub(self::prime(), $this->value));
    }

    public function inv(): self
    {
        $inv = gmp_invert($this->value, self::prime());
        if ($inv === false) {
            throw new \RuntimeException('Fp: element has no inverse (is zero)');
        }

        return new self($inv);
    }

    public function pow(GMP $exp): self
    {
        return new self(gmp_powm($this->value, $exp, self::prime()));
    }

    // -------------------------------------------------------------------------
    // Predicates
    // -------------------------------------------------------------------------

    public function isZero(): bool
    {
        return gmp_sign($this->value) === 0;
    }

    public function equals(self $other): bool
    {
        return gmp_cmp($this->value, $other->value) === 0;
    }

    /** sgn0: returns true if value is odd (used for hash-to-curve SWU) */
    public function isOdd(): bool
    {
        return gmp_testbit($this->value, 0);
    }

    /** True if value > (p-1)/2 (used for ZCash compressed point sign bit) */
    public function isNegative(): bool
    {
        // p is odd, so (p-1)/2 = p >> 1 in integer arithmetic
        return gmp_cmp($this->value, gmp_div(self::prime(), 2)) > 0;
    }

    // -------------------------------------------------------------------------
    // Serialization
    // -------------------------------------------------------------------------

    /** Returns 48-byte big-endian representation */
    public function toBytes(): string
    {
        $hex = str_pad(gmp_strval($this->value, 16), 96, '0', STR_PAD_LEFT);

        return hex2bin($hex);
    }

}
