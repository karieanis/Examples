<?php
namespace Examples\HiveTransformETL\Buffer;

/**
 * Standard buffer interface
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
interface IBuffer {
    /**
     * Write the passed input into the buffer
     * @param string $input
     */
    public function write($input);
    /**
     * Read len bytes from the buffer and return it
     * @param number $len
     * @return string
     */
    public function read($len);
    /**
     * How many bytes are available within the buffer?
     * @return number
     */
    public function available();
}