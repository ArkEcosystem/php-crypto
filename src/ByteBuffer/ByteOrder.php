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

namespace ArkEcosystem\Crypto\ByteBuffer;

class ByteOrder
{
    /**
     * Most significant value in the sequence is stored first. Flip no bytes!
     */
    public const BE = 0;

    /**
     * Least significant value in the sequence is stored first. Flip bytes!
     */
    public const LE = 1;

    /**
     * Let the current machine determine the endianess.
     */
    public const MB = 2;
}
