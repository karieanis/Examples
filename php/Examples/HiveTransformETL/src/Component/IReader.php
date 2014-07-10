<?php
namespace Examples\HiveTransformETL\Component;

/**
 * Interface defines a reader
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
interface IReader {
    /**
     * Perform a read operation
     */
    public function read();
}