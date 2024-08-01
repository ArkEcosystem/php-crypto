<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\ByteBuffer\Concerns;

/**
 * This is the writeable trait.
 */
trait Writeable
{
    use Writes\Floats;
    use Writes\Hex;
    use Writes\Integer;
    use Writes\Strings;
    use Writes\UnsignedInteger;
}
