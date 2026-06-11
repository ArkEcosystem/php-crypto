<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\BLS\HashToCurve;

use ArkEcosystem\Crypto\BLS\Curves\G2;
use ArkEcosystem\Crypto\BLS\Fields\Fp;
use ArkEcosystem\Crypto\BLS\Fields\Fp2;

/**
 * Hash-to-G2 for BLS12-381 following RFC 9380 §8.8.2 with SHA-256 XMD.
 * Pipeline: expand_message_xmd → hash_to_field → SWU map → 3-isogeny → cofactor clear.
 */
final class G2HashToCurve
{
    // RFC 9380 §8.8.2 cipher-suite: BLS12381G2_XMD:SHA-256_SSWU_RO_
    // SHA-256 parameters
    private const B_IN_BYTES = 32;  // sha256 output bytes
    private const R_IN_BYTES = 64;  // sha256 block size (zero-pad length)

    // SWU constants for the isogenous G2 curve E': y² = x³ + A'x + B'
    // A' = 240·u (Fp2: c0=0, c1=240)
    // B' = 1012 + 1012·u (Fp2: c0=1012, c1=1012)
    // Z  = -(2 + u) (Fp2: c0=p-2, c1=p-1)

    // Isogeny map G2 coefficients (ascending order k_{?,0} to k_{?,d})
    // Maps the SWU output on E' to the actual BLS12-381 G2 curve E.
    private const ISO_XN = [
        ['5c759507e8e333ebb5b7a9a47d7ed8532c52d39fd3a042a88b58423c50ae15d5c2638e343d9c71c6238aaaaaaaa97d6',
         '5c759507e8e333ebb5b7a9a47d7ed8532c52d39fd3a042a88b58423c50ae15d5c2638e343d9c71c6238aaaaaaaa97d6'],
        ['0',
         '11560bf17baa99bc32126fced787c88f984f87adf7ae0c7f9a208c6b4f20a4181472aaa9cb8d555526a9ffffffffc71a'],
        ['11560bf17baa99bc32126fced787c88f984f87adf7ae0c7f9a208c6b4f20a4181472aaa9cb8d555526a9ffffffffc71e',
         '8ab05f8bdd54cde190937e76bc3e447cc27c3d6fbd7063fcd104635a790520c0a395554e5c6aaaa9354ffffffffe38d'],
        ['171d6541fa38ccfaed6dea691f5fb614cb14b4e7f4e810aa22d6108f142b85757098e38d0f671c7188e2aaaaaaaa5ed1',
         '0'],
    ];
    private const ISO_XD = [
        ['0',
         '1a0111ea397fe69a4b1ba7b6434bacd764774b84f38512bf6730d2a0f6b0f6241eabfffeb153ffffb9feffffffffaa63'],
        ['c',
         '1a0111ea397fe69a4b1ba7b6434bacd764774b84f38512bf6730d2a0f6b0f6241eabfffeb153ffffb9feffffffffaa9f'],
        ['1', '0'], // leading 1 (monic)
    ];
    private const ISO_YN = [
        ['1530477c7ab4113b59a4c18b076d11930f7da5d4a07f649bf54439d87d27e500fc8c25ebf8c92f6812cfc71c71c6d706',
         '1530477c7ab4113b59a4c18b076d11930f7da5d4a07f649bf54439d87d27e500fc8c25ebf8c92f6812cfc71c71c6d706'],
        ['0',
         '5c759507e8e333ebb5b7a9a47d7ed8532c52d39fd3a042a88b58423c50ae15d5c2638e343d9c71c6238aaaaaaaa97be'],
        ['11560bf17baa99bc32126fced787c88f984f87adf7ae0c7f9a208c6b4f20a4181472aaa9cb8d555526a9ffffffffc71c',
         '8ab05f8bdd54cde190937e76bc3e447cc27c3d6fbd7063fcd104635a790520c0a395554e5c6aaaa9354ffffffffe38f'],
        ['124c9ad43b6cf79bfbf7043de3811ad0761b0f37a1e26286b0e977c69aa274524e79097a56dc4bd9e1b371c71c718b10',
         '0'],
    ];
    private const ISO_YD = [
        ['1a0111ea397fe69a4b1ba7b6434bacd764774b84f38512bf6730d2a0f6b0f6241eabfffeb153ffffb9feffffffffa8fb',
         '1a0111ea397fe69a4b1ba7b6434bacd764774b84f38512bf6730d2a0f6b0f6241eabfffeb153ffffb9feffffffffa8fb'],
        ['0',
         '1a0111ea397fe69a4b1ba7b6434bacd764774b84f38512bf6730d2a0f6b0f6241eabfffeb153ffffb9feffffffffa9d3'],
        ['12',
         '1a0111ea397fe69a4b1ba7b6434bacd764774b84f38512bf6730d2a0f6b0f6241eabfffeb153ffffb9feffffffffaa99'],
        ['1', '0'], // leading 1 (monic)
    ];

    // Cached SWU sqrtRatio precomputed constants (c6 and c7 in RFC 9380 Appendix F.2.1.2)
    private static ?Fp2 $swuC6 = null;
    private static ?Fp2 $swuC7 = null;

    /**
     * Hash a byte string to a G2 point using the POP-DST.
     * Input: arbitrary bytes (e.g., a compressed G1 public key for PoP).
     */
    public static function hashToG2(string $msg, string $dst): G2
    {
        // RFC 9380: L = ceil((log2(p) + k) / 8) = ceil((381 + 128) / 8) = 64
        $L             = 64;
        $count         = 2;   // hash_to_field produces 2 Fp2 elements for hash_to_curve
        $m             = 2;   // extension degree of Fp2
        $lenInBytes    = $count * $m * $L; // 2 * 2 * 64 = 256

        $uniformBytes = self::expandMessageXmd($msg, $dst, $lenInBytes);

        // hash_to_field: split into (count * m) chunks of L bytes, reduce mod p
        [$u0, $u1] = self::uniformBytesToFp2Pair($uniformBytes, $L);

        $q0 = self::mapToCurveG2($u0);
        $q1 = self::mapToCurveG2($u1);

        return $q0->add($q1)->clearCofactor();
    }

    // =========================================================================
    // expand_message_xmd (RFC 9380 §5.3.1, SHA-256 variant)
    // =========================================================================

    private static function expandMessageXmd(string $msg, string $dst, int $lenInBytes): string
    {
        $ell       = (int) ceil($lenInBytes / self::B_IN_BYTES); // = 8 for 256 bytes
        $dstPrime  = $dst . chr(strlen($dst));
        $zPad      = str_repeat("\x00", self::R_IN_BYTES);
        $libStr    = chr(($lenInBytes >> 8) & 0xff) . chr($lenInBytes & 0xff);

        $b0 = hash('sha256', $zPad . $msg . $libStr . "\x00" . $dstPrime, true);
        $b  = [];
        $b[0] = hash('sha256', $b0 . "\x01" . $dstPrime, true);

        for ($i = 1; $i < $ell; $i++) {
            $b[$i] = hash('sha256', self::strxor($b0, $b[$i - 1]) . chr($i + 1) . $dstPrime, true);
        }

        return substr(implode('', $b), 0, $lenInBytes);
    }

    private static function strxor(string $a, string $b): string
    {
        $out = '';
        $len = strlen($a);
        for ($i = 0; $i < $len; $i++) {
            $out .= chr(ord($a[$i]) ^ ord($b[$i]));
        }
        return $out;
    }

    // =========================================================================
    // hash_to_field: 256 uniform bytes → two Fp2 elements
    // Layout: [e0_c0 (64B), e0_c1 (64B), e1_c0 (64B), e1_c1 (64B)]
    // =========================================================================

    /**
     * @return Fp2[]  [u0, u1]
     */
    private static function uniformBytesToFp2Pair(string $bytes, int $L): array
    {
        // m=2 extension, count=2 elements
        // offset = L * (j + i * m)
        // e0: j=0 → offset 0, j=1 → offset 64
        // e1: j=0 → offset 128, j=1 → offset 192
        $p  = Fp::prime();
        $u0 = new Fp2(
            new Fp(gmp_mod(gmp_init(bin2hex(substr($bytes, 0, $L)), 16), $p)),
            new Fp(gmp_mod(gmp_init(bin2hex(substr($bytes, $L, $L)), 16), $p))
        );
        $u1 = new Fp2(
            new Fp(gmp_mod(gmp_init(bin2hex(substr($bytes, 2 * $L, $L)), 16), $p)),
            new Fp(gmp_mod(gmp_init(bin2hex(substr($bytes, 3 * $L, $L)), 16), $p))
        );
        return [$u0, $u1];
    }

    // =========================================================================
    // SWU map: Fp2 element → affine point on isogenous E'
    // Then apply the 3-isogeny to land on actual G2 curve E.
    // =========================================================================

    private static function mapToCurveG2(Fp2 $u): G2
    {
        // SWU curve parameters
        // A' = 240·u
        $A = Fp2::fromInts(0, 240);
        // B' = 1012 + 1012·u
        $B = Fp2::fromInts(1012, 1012);
        // Z  = -(2 + u) = (p-2) + (p-1)·u
        $p  = Fp::prime();
        $Z  = new Fp2(new Fp(gmp_sub($p, gmp_init(2))), new Fp(gmp_sub($p, gmp_init(1))));
        $F1 = Fp2::one();

        // RFC 9380 §6.6.2 Simplified SWU, 25 steps (noble-curves numbering):
        // prettier-ignore
        $tv1 = $u->square();              // 1.  tv1 = u²
        $tv1 = $Z->mul($tv1);             // 2.  tv1 = Z * tv1
        $tv2 = $tv1->square();            // 3.  tv2 = tv1²
        $tv2 = $tv2->add($tv1);           // 4.  tv2 = tv2 + tv1
        $tv3 = $tv2->add($F1);            // 5.  tv3 = tv2 + 1
        $tv3 = $B->mul($tv3);             // 6.  tv3 = B * tv3
        // 7.  tv4 = CMOV(Z, -tv2, tv2 != 0)
        $tv4 = Fp2::cmov($Z, $tv2->neg(), !$tv2->isZero());
        $tv4 = $A->mul($tv4);             // 8.  tv4 = A * tv4
        $tv2 = $tv3->square();            // 9.  tv2 = tv3²
        $tv6 = $tv4->square();            // 10. tv6 = tv4²
        $tv5 = $A->mul($tv6);             // 11. tv5 = A * tv6
        $tv2 = $tv2->add($tv5);           // 12. tv2 = tv2 + tv5
        $tv2 = $tv2->mul($tv3);           // 13. tv2 = tv2 * tv3
        $tv6 = $tv6->mul($tv4);           // 14. tv6 = tv6 * tv4
        $tv5 = $B->mul($tv6);             // 15. tv5 = B * tv6
        $tv2 = $tv2->add($tv5);           // 16. tv2 = tv2 + tv5
        $x   = $tv1->mul($tv3);           // 17.   x = tv1 * tv3

        // 18. (is_gx1_square, y1) = sqrt_ratio(tv2, tv6)
        ['isValid' => $isValid, 'value' => $y1] = self::sqrtRatio($tv2, $tv6);

        $y   = $tv1->mul($u);             // 19.   y = tv1 * u   (= Z·u³ * y_coeff)
        $y   = $y->mul($y1);              // 20.   y = y * y1

        $x   = Fp2::cmov($x, $tv3, $isValid);  // 21.   x = CMOV(x, tv3, is_gx1_square)
        $y   = Fp2::cmov($y, $y1, $isValid);   // 22.   y = CMOV(y, y1, is_gx1_square)

        // 23. e1 = (sgn0(u) == sgn0(y))
        $e1  = ($u->isOdd() === $y->isOdd());
        // 24. y = CMOV(-y, y, e1)
        $y   = Fp2::cmov($y->neg(), $y, $e1);

        // 25. x = x / tv4
        $x   = $x->mul($tv4->inv());

        // Apply 3-isogeny to get point on actual G2 curve
        [$xi, $yi] = self::applyIsogeny($x, $y);

        // Return as Jacobian G2 point (Z=1)
        return new G2($xi, $yi, Fp2::one());
    }

    // =========================================================================
    // RFC 9380 Appendix F.2.1.2 — sqrt_ratio for Fp2 (q ≡ 9 mod 16, c1=3)
    // Returns { isValid, value } where value = sqrt(u/v) if QR, else sqrt(Z·u/v)
    // =========================================================================

    /**
     * @return array{isValid: bool, value: Fp2}
     */
    private static function sqrtRatio(Fp2 $u, Fp2 $v): array
    {
        [$c6, $c7] = self::swuPrecompute();

        // The algorithm uses small-power exponents that we compute with Fp2::pow
        $c4 = gmp_init(7);  // 2^c1 - 1 = 7
        $c5 = gmp_init(4);  // 2^(c1-1) = 4
        $c3 = self::swuC3();

        // Steps 1-16
        $tv1 = $c6;                                   // 1
        $tv2 = self::fp2Pow7($v);                     // 2. v^7
        $tv3 = $tv2->square()->mul($v);               // 3-4. v^14 * v = v^15
        $tv5 = $u->mul($tv3);                         // 5. u * v^15
        $tv5 = $tv5->pow($c3);                        // 6. (u * v^15)^c3
        $tv5 = $tv5->mul($tv2);                       // 7. * v^7
        $tv2 = $tv5->mul($v);                         // 8.
        $tv3 = $tv5->mul($u);                         // 9.
        $tv4 = $tv3->mul($tv2);                       // 10.
        $tv5 = $tv4->pow($c5);                        // 11. tv4^4
        $isQR = $tv5->equals(Fp2::one());             // 12.
        $tv2 = $tv3->mul($c7);                        // 13.
        $tv5 = $tv4->mul($tv1);                       // 14. tv4 * c6
        $tv3 = Fp2::cmov($tv2, $tv3, $isQR);          // 15.
        $tv4 = Fp2::cmov($tv5, $tv4, $isQR);          // 16.

        // Steps 17-26: loop i = c1 downto 2 (i.e., i = 3 then i = 2)
        for ($i = 3; $i >= 2; $i--) {
            // tv5 = 2^(i-2): for i=3 → 2, for i=2 → 1
            $powExp = ($i === 3) ? 2 : 1;
            $tvv5   = ($powExp === 2) ? $tv4->square() : $tv4; // tv4^powExp
            $e1     = $tvv5->equals(Fp2::one());              // 21.
            $tv2    = $tv3->mul($tv1);                         // 22.
            $tv1    = $tv1->square();                          // 23.
            $tvv5   = $tv4->mul($tv1);                         // 24.
            $tv3    = Fp2::cmov($tv2, $tv3, $e1);              // 25.
            $tv4    = Fp2::cmov($tvv5, $tv4, $e1);             // 26.
        }

        $isValid = !$v->isZero() && ($isQR || $u->isZero());
        return ['isValid' => $isValid, 'value' => $tv3];
    }

    /** Fp2^7 via binary method (3 squarings + 3 muls) */
    private static function fp2Pow7(Fp2 $v): Fp2
    {
        $v2 = $v->square();   // v²
        $v4 = $v2->square();  // v⁴
        return $v4->mul($v2)->mul($v); // v⁶ * v = v⁷
    }

    // =========================================================================
    // Precomputation of sqrtRatio constants c6 and c7 (lazily cached)
    // c1 = 3, q = p², c2 = (q-1)/8, c3 = (c2-1)/2
    // c6 = Z^c2, c7 = Z^((c2+1)/2)
    // =========================================================================

    /** @return Fp2[] [c6, c7] */
    private static function swuPrecompute(): array
    {
        if (self::$swuC6 !== null) {
            return [self::$swuC6, self::$swuC7];
        }

        $p  = Fp::prime();
        $q  = gmp_mul($p, $p);          // q = p²
        $q1 = gmp_sub($q, gmp_init(1)); // q - 1
        $c2 = gmp_div($q1, gmp_init(8)); // (q-1) / 8

        // Z = -(2 + u) = (p-2) + (p-1)·u
        $Z  = new Fp2(new Fp(gmp_sub($p, gmp_init(2))), new Fp(gmp_sub($p, gmp_init(1))));

        self::$swuC6 = $Z->pow($c2);
        self::$swuC7 = $Z->pow(gmp_div(gmp_add($c2, gmp_init(1)), gmp_init(2)));

        return [self::$swuC6, self::$swuC7];
    }

    private static ?Fp2 $swuC3cached = null;

    private static function swuC3(): \GMP
    {
        static $c3 = null;
        if ($c3 === null) {
            $p  = Fp::prime();
            $q  = gmp_mul($p, $p);
            $c2 = gmp_div(gmp_sub($q, gmp_init(1)), gmp_init(8));
            $c3 = gmp_div(gmp_sub($c2, gmp_init(1)), gmp_init(2));
        }
        return $c3;
    }

    // =========================================================================
    // 3-isogeny: E' → G2 curve E (rational map via Horner evaluation)
    // =========================================================================

    /** @return Fp2[] [x, y] */
    private static function applyIsogeny(Fp2 $x, Fp2 $y): array
    {
        $xn = self::horner(self::ISO_XN, $x);
        $xd = self::horner(self::ISO_XD, $x);
        $yn = self::horner(self::ISO_YN, $x);
        $yd = self::horner(self::ISO_YD, $x);

        $xi = $xn->mul($xd->inv());
        $yi = $y->mul($yn->mul($yd->inv()));

        return [$xi, $yi];
    }

    /**
     * Horner evaluation of a polynomial at x.
     * Coefficients in ascending order [k0, k1, ..., kd] are reversed for Horner.
     * Result: k0 + k1*x + ... + kd*x^d = horner([kd,...,k1,k0], x)
     */
    private static function horner(array $ascCoeffs, Fp2 $x): Fp2
    {
        $coeffs = array_reverse(
            array_map(function ($pair) {
                // '0' is falsy in PHP so use explicit comparison
                $c0 = ($pair[0] !== '' && $pair[0] !== null) ? $pair[0] : '0';
                $c1 = ($pair[1] !== '' && $pair[1] !== null) ? $pair[1] : '0';
                return Fp2::fromHex($c0, $c1);
            }, $ascCoeffs)
        );
        $acc = $coeffs[0];
        for ($i = 1, $n = count($coeffs); $i < $n; $i++) {
            $acc = $acc->mul($x)->add($coeffs[$i]);
        }
        return $acc;
    }
}
