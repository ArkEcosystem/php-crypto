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

namespace ArkEcosystem\Crypto\Deserialisers;

use BrianFaust\Binary\UnsignedInteger\Reader as UnsignedInteger;
use stdClass;

/**
 * This is the deserialiser class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class Vote extends AbstractDeserialiser
{
    /**
     * Handle the deserialisation of "second signature registration" data.
     *
     * @return object
     */
    public function deserialise(): object
    {
        $voteLength = UnsignedInteger::bit8($this->binary, $this->assetOffset / 2) & 0xff;

        $this->transaction->asset        = new stdClass();
        $this->transaction->asset->votes = [];

        $vote = null;
        for ($i = 0; $i < $voteLength; ++$i) {
            $vote                                  = substr($this->hex, $this->assetOffset + 2 + $i * 2 * 34, 2 * 34);
            $vote                                  = ('1' === $vote[1] ? '+' : '-').substr($vote, 2);
            $this->transaction->asset->votes[]     = $vote;
        }

        return $this->parseSignatures($this->assetOffset + 2 + $voteLength * 34 * 2);
    }
}
