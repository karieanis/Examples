<?php
namespace Examples\ThriftServices\Hive\Meta;

/**
 * Represents a collection of value property accessors for a HiveServer2 result set
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
class PropertyMap extends \Director\Lib\Util\Collection {
    /**
     * An array of potential value property accessors
     * @var array
     */
    protected static $types = array(
        "boolVal",
        "byteVal",
        "i16Val",
        "i32Val",
        "i64Val",
        "doubleVal",
        "stringVal"
    );
    
    /**
     * Manufacture a metadata object utilising the passed TRow object. This metadata contains a reference to the
     * appropriate value property accessor for each column.
     * @param \apache\hive\service\cli\thrift\TRow $row
     */
    public static function factory(\apache\hive\service\cli\thrift\TRow $row) {
        $map = new static();
        
        foreach($row->colVals as $col) {
            $map->add(static::findProperty($col));
        }
        
        return $map;
    }

    /**
     * Find the property within the passed TColumnValue which currently contains a value. The matching property type
     * will be returned.
     * @param \apache\hive\service\cli\thrift\TColumnValue $value
     * @return string
     */
    protected static function findProperty(\apache\hive\service\cli\thrift\TColumnValue $value) {
        $type = null;
        $types = static::$types;
        
        for(reset($types); is_null($type) && false !== ($prop = current($types)); next($types)) {
            if(!is_null($value->{$prop})) {
                $type = $prop;
            }
        }
        
        return $type;
    }
}