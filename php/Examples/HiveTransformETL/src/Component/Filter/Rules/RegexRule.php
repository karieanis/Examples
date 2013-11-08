<?php
namespace Examples\HiveTransformETL\Component\Filter\Rules;

/**
 * Application layer regular expression rule wrapper. Applies the regular expression retrieved from the data model
 * to the passed value
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
class RegexRule extends BaseRule implements IRule {
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Component\Filter\Rules\IRule::apply()
     */
    public function apply($value) {
        return @preg_match("@" . $this->getRuleModel()->getRegex() . "@i", $value) > 0;
    }
}