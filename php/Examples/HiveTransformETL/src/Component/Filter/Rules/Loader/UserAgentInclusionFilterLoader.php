<?php
namespace Examples\HiveTransformETL\Component\Filter\Rules\Loader;

use \Examples\HiveTransformETL\Component\Filter\UserAgentInclusionFilter,
    \Examples\HiveTransformETL\Component\Filter\Rules\RuleConf,
    \Examples\HiveTransformETL\Component\Filter\Rules\RuleCache,
    \Examples\HiveTransformETL\Component\Filter\Rules\UserAgentWhitelistRule,
    \Examples\HiveTransformETL\Component\Deserializer\TokenizedDeserializer,
    \Examples\HiveTransformETL\Component\Reader,
    \Examples\HiveTransformETL\Schema\SchemaFactory,
    \Examples\HiveTransformETL\Model\UserAgentRuleRow;

/**
 * Loader response for loading user agent inclusion rules into a user agent inclusion filter
 * @final
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
final class UserAgentInclusionFilterLoader {
    /**
     * Load rules into the filter using the conf object
     * @param UserAgentInclusionFilter $filter
     * @param RuleConf $conf
     */
    public static function load(UserAgentInclusionFilter $filter, RuleConf $conf) {
        $cache = RuleCache::getInstance();
        
        // check to see if we already have rules for the filter stored in the rule cache
        if(is_null($rules = $cache->get($filter))) {
            $rules = array();
        
            // build a file stream string
            $file = "file://" . realpath($conf->getUserAgentRulesDir() . DIRECTORY_SEPARATOR . "include.txt");
            $deserializer = TokenizedDeserializer::instance()->setToken("|"); // create a new tokenized deserializer
            $reader = new Reader($file); // create a new reader for the file stream
            $reader->open(); // open the file stream for read operations
        
            // whilst there a lines to read
            while(false !== ($line = $reader->read())) {
                // if the line isn't a comment and can be deserialized
                if(preg_match("/^#/", $line) === 0 && ($deserialized = $deserializer->deserialize($line))) {
                    // wrap the rule data model in a application layer model
                    $rule = UserAgentWhitelistRule::wrap(
                        UserAgentRuleRow::create(
                            $deserialized,
                            SchemaFactory::factory("\Examples\HiveTransformETL\Schema\Row\UserAgentWhitelistRule")
                        )
                    );
                    
                    // if this isn't a deprecated rule
                    if(!$rule->isDeprecated()) {
                        array_push($rules, $rule); // add it to the array
                    }
                }
            }
        
            // add the rules to the cache
            $cache->add($filter, $rules);
        }
        
        // set the rules for the filter
        $filter->setRules($rules);
    }
}