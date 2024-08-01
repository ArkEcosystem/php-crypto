<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\ByteBuffer\Concerns;

/**
 * This is the readable trait.
 */
trait Readable
{
    use Reads\Floats;
    use Reads\Hex;
    use Reads\Integer;
    use Reads\Strings;
    use Reads\UnsignedInteger;
}
