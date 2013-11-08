<?php
namespace Examples\HiveTransformETL\Schema;

/**
 * Basic schema map abstract implementation
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
abstract class AbstractSchemaMap implements ISchemaMap {
    /**
     * Populate in concrete implementation classes
     * @staticvar array
     */
    protected static $map;
    
    /**
     * @codeCoverageIgnore
     */
    public function __construct() {
        
    }
    
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Schema\ISchemaMap::getNameAt()
     */
    public function getNameAt($index) {
        return isset(static::$map[$index]) ? static::$map[$index] : false;
    }
    
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Schema\ISchemaMap::getIndexFor()
     */
    public function getIndexFor($field) {
        return array_search($field, static::$map);
    }
}