<?php
namespace Examples\HiveTransformETL\Decoder;

/**
 * Basic interface for decoder implementations
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
interface IDecoder {
    /**
     * Decode the passed value
     * @param mixed $value
     * @return mixed
     */
    public function decode($value);
}