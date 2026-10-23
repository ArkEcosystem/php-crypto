<?php

declare(strict_types=1);

/*
 * This file is part of Ark PHP Crypto.
 *
 * (c) Ark Ecosystem <info@ark.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ArkEcosystem\Crypto\Networks;

/**
 * This is the abstract network class.
 *
 * @author Brian Faust <brian@ark.io>
 */
abstract class AbstractNetwork
{
    public const BASE58_ADDRESS_P2PKH = 'p2pkh';

    public const BASE58_ADDRESS_P2SH = 'p2sh';

    public const BASE58_WIF = 'wif';

    public const BIP32_PREFIX_XPUB = 'xpub';

    public const BIP32_PREFIX_XPRV = 'xprv';

    /**
     * The hex encoded version bytes for Base58 encoded data.
     *
     * @var array<string, string>
     */
    protected $base58PrefixMap = [];

    /**
     * The hex encoded version bytes for BIP32 extended keys.
     *
     * @var array<string, string>
     */
    protected $bip32PrefixMap = [];

    /**
     * Create a new network instance.
     *
     * @return static
     */
    public static function new()
    {
        return new static();
    }

    /**
     * Get the network version as number.
     *
     * @return int
     */
    public function version(): int
    {
        return (int) hexdec($this->getAddressByte());
    }

    /**
     * Get the hex encoded version byte of pay-to-public-key-hash addresses.
     *
     * @return string
     */
    public function getAddressByte(): string
    {
        return $this->base58PrefixMap[self::BASE58_ADDRESS_P2PKH];
    }

    /**
     * Get the hex encoded version byte of pay-to-script-hash addresses.
     *
     * @return string
     */
    public function getP2shByte(): string
    {
        return $this->base58PrefixMap[self::BASE58_ADDRESS_P2SH];
    }

    /**
     * Get the hex encoded version byte of WIF private keys.
     *
     * @return string
     */
    public function getPrivByte(): string
    {
        return $this->base58PrefixMap[self::BASE58_WIF];
    }

    /**
     * Get the hex encoded BIP32 prefix of extended public keys.
     *
     * @return string
     */
    public function getHDPubByte(): string
    {
        return $this->bip32PrefixMap[self::BIP32_PREFIX_XPUB];
    }

    /**
     * Get the hex encoded BIP32 prefix of extended private keys.
     *
     * @return string
     */
    public function getHDPrivByte(): string
    {
        return $this->bip32PrefixMap[self::BIP32_PREFIX_XPRV];
    }

    abstract public function pubKeyHash(): int;

    abstract public function epoch(): string;
}
