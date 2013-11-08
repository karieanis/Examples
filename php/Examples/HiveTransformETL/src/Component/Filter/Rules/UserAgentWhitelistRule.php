<?php
namespace Examples\HiveTransformETL\Component\Filter\Rules;

use \Examples\HiveTransformETL\Model\Wrapper\UserAgentWhitelistRuleWrapper as Wrapper;

/**
 * Encapsulates the logic of applying whitelist rules to a value. The rules data is sourced from the internal rule model
 * object.
 * 
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
class UserAgentWhitelistRule extends BaseRule implements IRule, IDeprecatableRule {
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Component\Filter\Rules\IRule::apply()
     */
    public function apply($value) {
        $rule = Wrapper::fly($this->getRuleModel());
        $matchFromStart = $rule->matchUserAgentFromStart();

        // value is whitelisting if it matches the internal rule value and match position (where applicable)
        return false !== ($result = stripos($value, $rule->getUserAgent(),  $matchFromStart ? 0 : NULL)) && 
            $matchFromStart ? $result === 0 : true;
    }

    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Component\Filter\Rules\IDeprecatableRule::isDeprecated()
     */
    public function isDeprecated() {
        return !(Wrapper::fly($this->getRuleModel())->isActive());
    }
}