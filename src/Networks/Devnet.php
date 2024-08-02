<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Networks;

class Devnet extends AbstractNetwork
{
    /**
     * {@inheritdoc}
     *
     * @see Network::$base58PrefixMap
     */
    protected $base58PrefixMap = [
        self::BASE58_ADDRESS_P2PKH => '1e',
        self::BASE58_ADDRESS_P2SH  => '00',
        self::BASE58_WIF           => 'aa',
    ];

    /**
     * {@inheritdoc}
     *
     * @see Network::$bip32PrefixMap
     */
    protected $bip32PrefixMap = [
        self::BIP32_PREFIX_XPUB => '46090600',
        self::BIP32_PREFIX_XPRV => '46089520',
    ];

    /**
     * {@inheritdoc}
     */
    public function pubKeyHash(): int
    {
        return 30;
    }

    /**
     * {@inheritdoc}
     */
    public function epoch(): string
    {
        return '2017-03-21T13:00:00.000Z';
    }
}
