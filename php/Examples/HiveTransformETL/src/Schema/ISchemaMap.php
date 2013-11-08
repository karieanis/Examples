<?php
namespace Examples\HiveTransformETL\Schema;

/**
 * Basic schema interface
 * @author Jeremy Rayner <jeremy@davros.com.au>
 * @codeCoverageIgnore
 *
 */
interface ISchemaMap {
    /**
     * Retrieve the schema field name for the passed index location
     * @param int $index        The index
     * @return string           The field name
     */
    public function getNameAt($index);
    /**
     * Retrieve the index for the passed schema field name
     * @param string $field     The field name
     * @return int              The index
     */
    public function getIndexFor($field);
}