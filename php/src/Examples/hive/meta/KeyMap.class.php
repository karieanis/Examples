<?php
namespace Examples\Hive\Meta;

/**
 * Represents a collection of column names for a HiveServer2 result set
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
class KeyMap extends \Director\Lib\Util\Collection {
    /**
     * Manufacture a key map containing the column names of a HiveServer2 result set
     * @param \TTableSchema $schema
     * @return \Examples\Hive\Meta\KeyMap
     */
    public static function factory(\TTableSchema $schema) {
        $map = new static();

        /* @var \TColumnDesc $col */
        foreach($schema->columns as $col) {
            $map->add($col->columnName);
        }
        
        return $map;
    }
}