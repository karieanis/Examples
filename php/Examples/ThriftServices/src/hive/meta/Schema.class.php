<?php
namespace Examples\ThriftServices\Hive\Meta;

/**
 * Metadata schema holder. Used as a one stop shop for key and property map object which are utilised by the
 * HivePDO classes
 * @author Jeremy Rayner <jeremy@davros.com.au>
 * @codeCoverageIgnore
 */
class Schema {
    /**
     * The metadata for column keys
     * @var KeyMap
     */
    protected $keyMap;
    /**
     * The metadata for value property accessors
     * @var PropertyMap
     */
    protected $propertyMap;
    
    /**
     * Constructor
     */
    public function __construct() {
        
    }
    
    /**
     * Get the current key map metadata
     * @return \Examples\ThriftServices\Hive\Meta\KeyMap
     */
    public function getKeyMap() {
        return $this->keyMap;
    }
    
    /**
     * Get the current property map metadata
     * @return \Examples\ThriftServices\Hive\Meta\PropertyMap
     */
    public function getPropertyMap() {
        return $this->propertyMap;
    }
    
    /**
     * Set the key map metadata
     * @param KeyMap $map
     * @return \Examples\ThriftServices\Hive\Meta\Schema
     */
    public function setKeyMap(KeyMap $map) {
        $this->keyMap = $map;
        return $this;
    }
    
    /**
     * Set the property map metadata
     * @param PropertyMap $map
     * @return \Examples\ThriftServices\Hive\Meta\Schema
     */
    public function setPropertyMap(PropertyMap $map) {
        $this->propertyMap = $map;
        return $this;
    }
}