<?php
namespace Examples\HiveTransformETL\Component\SerDe;

use \Examples\HiveTransformETL\Component\Serializer\ISerializer,
    \Examples\HiveTransformETL\Component\Deserializer\IDeserializer;

/**
 * SerDe class. 
 * @codeCoverageIgnore
 * @final
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
final class SerDe implements ISerializer, IDeserializer {
    /**
     * @var ISerializer
     */
    protected $serializer;
    /**
     * @var IDeserializer
     */
    protected $deserializer;
    
    /**
     * Get an instance
     * @return \Examples\HiveTransformETL\Component\SerDe\SerDe
     */
    public static function instance() {
        return new static;
    }
    
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Component\Serializer\ISerializer::serialize()
    */
    public function serialize($input) {
        return $this->getSerializer()->serialize($input);
    }
    
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Component\Deserializer\IDeserializer::deserialize()
    */
    public function deserialize($input) {
        return $this->getDeserializer()->deserialize($input);
    }
    
    /**
     * Get the serializer
     * @return ISerializer
     */
    public function getSerializer() {
        return $this->serializer;
    }
    
    /**
     * Get the deserializer
     * @return IDeserializer
     */
    public function getDeserializer() {
        return $this->deserializer;
    }
    
    /**
     * Set the serializer
     * @param ISerializer $serializer
     * @return \Examples\HiveTransformETL\Component\SerDe\SerDe
     */
    public function setSerializer(ISerializer $serializer) {
        $this->serializer = $serializer;
        return $this;
    }
    
    /**
     * Set the deserializer
     * @param IDeserializer $deserializer
     * @return \Examples\HiveTransformETL\Component\SerDe\SerDe
     */
    public function setDeserializer(IDeserializer $deserializer) {
        $this->deserializer = $deserializer;
        return $this;
    }
}