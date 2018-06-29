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

namespace ArkEcosystem\Crypto\Models;

use ArkEcosystem\Crypto\Enums\Types;
use BitWasp\Buffertools\Buffer;
use stdClass;

/**
 * This is the transaction class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class Transaction
{
    /**
     * Perform AIP11 compliant serialisation.
     *
     * @return \BitWasp\Buffertools\Buffer
     */
    public function serialise(): Buffer
    {
        return Serialiser::new($serialised)->deserialise();
    }

    /**
     * Perform AIP11 compliant deserialisation.
     *
     * @return stdClass
     */
    public function deserialise(string $serialised): stdClass
    {
        return Deserialiser::new($serialised)->deserialise();
    }
}
