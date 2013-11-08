<?php
namespace Examples\HiveTransformETL\Component\Filter;

/**
 * Interface used to describe a filter which is applied based upon a row
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
interface IRowFilter {
    /**
     * Determine if filtration is required for the passed row
     * @param mixed $row
     * @return boolean
     */
    public function filter($row);
}