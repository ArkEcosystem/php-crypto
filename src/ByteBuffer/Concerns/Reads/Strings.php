<?php

declare(strict_types=1);



namespace ArkEcosystem\Crypto\ByteBuffer\Concerns\Reads;

/**
 * This is the strings reader trait.
 */
trait Strings
{
    /**
     * Reads an UTF8 encoded string. This is an alias of readUTF8String.
     *
     * @param int $length
     * @param int $offset
     *
     * @return string
     */
    public function readString(int $length, int $offset = 0): string
    {
        return $this->readUTF8String($length, $offset);
    }

    /**
     * Reads an UTF8 encoded string.
     *
     * @param int $length
     * @param int $offset
     *
     * @return string
     */
    public function readUTF8String(int $length, int $offset = 0): string
    {
        return mb_convert_encoding($this->unpack("a{$length}", $offset), 'ISO-8859-1', 'UTF-8');
    }

    /**
     * Reads a NULL-terminated UTF8 encoded string.
     *
     * @param int $length
     * @param int $offset
     *
     * @return string
     */
    public function readCString(int $length, int $offset = 0): string
    {
        return mb_convert_encoding($this->unpack("Z{$length}", $offset), 'ISO-8859-1', 'UTF-8');
    }

    /**
     * Reads a length as uint32 prefixed UTF8 encoded string.
     *
     * @param int $length
     * @param int $offset
     *
     * @return string
     */
    public function readIString(int $length, int $offset = 0): string
    {
        return $this->readString($length, 4 + $offset);
    }

    /**
     * Reads a length as varint32 prefixed UTF8 encoded string.
     *
     * @param int $length
     * @param int $offset
     *
     * @return string
     */
    public function readVString(int $length, int $offset = 0): string
    {
        return $this->readString($length, 1 + $offset);
    }
}
