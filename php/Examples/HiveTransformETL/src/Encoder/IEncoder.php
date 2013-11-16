<?php
namespace Examples\HiveTransformETL\Encoder;

/**
 * Basic interface for encoder implementations
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
interface IEncoder {
    /**
     * Encode the passed value
     * @param mixed $value
     * @return mixed
     */
    public function encode($value);
}