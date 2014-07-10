<?php
namespace Examples\ThriftServices\Hive\Meta;

/**
 * Represents a collection of column names for a HiveServer2 result set
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
class KeyMap extends \Director\Lib\Util\Collection {
    /**
     * Manufacture a key map containing the column names of a HiveServer2 result set
     * @param \apache\hive\service\cli\thrift\TTableSchema $schema
     * @return \Examples\ThriftServices\Hive\Meta\KeyMap
     */
    public static function factory(\apache\hive\service\cli\thrift\TTableSchema $schema) {
        $map = new static();

        /* @var \apache\hive\service\cli\thrift\TColumnDesc $col */
        foreach($schema->columns as $col) {
            $map->add($col->columnName);
        }
        
        return $map;
    }
}