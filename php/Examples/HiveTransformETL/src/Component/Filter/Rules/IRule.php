<?php
namespace Examples\HiveTransformETL\Component\Filter\Rules;

/**
 * Interface defines the methods required of a logical rule
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
interface IRule {
    /**
     * Apply logical filter rules to a value
     * @param mixed $value
     * @return boolean
     */
    public function apply($value);
}