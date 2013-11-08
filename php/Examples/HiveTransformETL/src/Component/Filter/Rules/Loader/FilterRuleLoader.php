<?php
namespace Examples\HiveTransformETL\Component\Filter\Rules\Loader;

use \Examples\HiveTransformETL\Application\ApplicationContext,
    \Examples\HiveTransformETL\Component\Filter\Rules\RuleConf,
    \Examples\HiveTransformETL\Util\ReflectionUtils;

/**
 * Abstraction layer used to obsufucate knowledge of how to load rules into a filter from the client code.
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
final class FilterRuleLoader {
    /**
     * Load the passed filter with rules
     * @param mixed $filter
     */
    public static function load($filter) {
        $conf = RuleConf::wrap(ApplicationContext::getInstance()->getConf());
        
        switch(ReflectionUtils::resolveClassName($filter)) {
            case "Examples\HiveTransformETL\Component\Filter\IpAddressExclusionFilter":
                IpAddressExclusionRuleLoader::load($filter, $conf);
            break;
            
            case "Examples\HiveTransformETL\Component\Filter\UserAgentInclusionFilter":
                UserAgentInclusionFilterLoader::load($filter, $conf);
            break;
            
            case "Examples\HiveTransformETL\Component\Filter\UserAgentExclusionFilter":
                UserAgentExclusionFilterLoader::load($filter, $conf);
            break;
        }
    }
}