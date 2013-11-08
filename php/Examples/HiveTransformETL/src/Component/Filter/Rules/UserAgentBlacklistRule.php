<?php
namespace Examples\HiveTransformETL\Component\Filter\Rules;

use \Examples\HiveTransformETL\Model\Wrapper\UserAgentBlacklistRuleWrapper as Wrapper;

/**
 * Encapsulates the logic of applying blacklisting rules to a value. The rule data is sourced from the internal rule model
 * object.
 * 
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
class UserAgentBlacklistRule extends BaseRule implements IRule, IDeprecatableRule {
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Component\Filter\Rules\IRule::apply()
     */
    public function apply($value) {
        $exempt = true; 
        $matched = false;
        
        $rule = Wrapper::fly($this->getRuleModel());
        $matchFromStart = $rule->matchUserAgentFromStart();
        
        // if we have a successful match
        if($matched = ((false !== ($result = stripos($value, $rule->getUserAgent(), $matchFromStart ? 0 : NULL))) &&
            ($matchFromStart ? $result === 0 : true))) {

            // check for any exemptions
            for($exceptions = $rule->getExceptions(), reset($exceptions), $exempt = false;
                false === $exempt && false !== ($exception = current($exceptions));
                next($exceptions)) {

                $exempt = false !== stristr($value, $exception);
            }
        }
        
        return $matched && !$exempt; // passes the rule if there is a match and it's not exempt
    }
    

    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Component\Filter\Rules\IDeprecatableRule::isDeprecated()
     */
    public function isDeprecated() {
        return !(Wrapper::fly($this->getRuleModel())->isActive());
    }
}