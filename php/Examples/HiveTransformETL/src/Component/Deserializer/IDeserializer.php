<?php
namespace Examples\HiveTransformETL\Component\Deserializer;

/**
 * Interface used to describe a deserializer
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
interface IDeserializer {
    /**
     * Deserialize the input
     * @param mixed $input        Something to deserialize
     * @return mixed              The deserialized object
     */
    public function deserialize($input);
}