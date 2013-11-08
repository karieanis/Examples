<?php
namespace Examples\HiveTransformETL\Component\Serializer;

/**
 * Interface used to describe a serializer
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
interface ISerializer {
    /**
     * Serialize the input
     * @param mixed $input        Something to serialize
     * @return mixed              The serialized object
     */
    public function serialize($input);
}