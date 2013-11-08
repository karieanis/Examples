<?php
namespace Examples\HiveTransformETL\Component\Filter\Rules;

/**
 * Interface defines the methods required of a rule which can be deprecated
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
interface IDeprecatableRule {
    /**
     * Check if the rule has been deprecated
     * @return boolean
     */
    public function isDeprecated();
}