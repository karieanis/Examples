<?php
namespace Examples\HiveTransformETL\Component\Filter\Rules\Loader;

use \Examples\HiveTransformETL\Component\Filter\IpAddressExclusionFilter,
    \Examples\HiveTransformETL\Component\Filter\Rules\RuleCache,
    \Examples\HiveTransformETL\Component\Filter\Rules\RuleConf,
    \Examples\HiveTransformETL\Component\Filter\Rules\RegexRule,
    \Examples\HiveTransformETL\Component\Reader,
    \Examples\HiveTransformETL\Model\IpExclusionRule;

/**
 * Loader responsible for loading ip exclusion rules into an ip address exculsion filter
 * @final
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
final Class IpAddressExclusionRuleLoader {
    /**
     * Load rules into the filter using the conf object
     * @param IpAddressExclusionFilter $filter
     * @param RuleConf $conf
     */
    public static function load(IpAddressExclusionFilter $filter, RuleConf $conf) {
        $cache = RuleCache::getInstance();
        
        // check to see if we already have rules for the filter stored in the rule cache
        if(is_null($rules = $cache->get($filter))) {
            $rules = array();
        
            // build a file stream string
            $file = "file://" . realpath($conf->getIpRulesDir() . DIRECTORY_SEPARATOR . "exclude.txt");
            $reader = new Reader($file); // create a new reader for the file stream
            $reader->open(); // open the file stream for read operations
        
            // while we have rules to read
            while(false !== ($regex = $reader->read())) {
                // add the rules to the rule array
                array_push($rules, RegexRule::wrap(
                        IpExclusionRule::instance()->setRegex($regex)
                    )
                );
            }
        
            // add the rules to the cache
            $cache->add($filter, $rules);
        }
        
        // set the rules for the filter
        $filter->setRules($rules);
    }
}