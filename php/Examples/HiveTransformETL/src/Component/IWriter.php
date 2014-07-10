<?php
namespace Examples\HiveTransformETL\Component;

/**
 * Interface describe a writer
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
interface IWriter {
    /**
     * Write the passed variable somewhere
     * @param mixed $output
     */
    public function write($output);
}