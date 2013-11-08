<?php
namespace Examples\HiveTransformETL\Component\Filter;

/**
 * Interface used to describe a filter which is applied based upon a field value
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
interface IFieldValueFilter {
    /**
     * Filter the value
     * @param mixed $value
     * @return boolean
     */
    public function filter($value);
}