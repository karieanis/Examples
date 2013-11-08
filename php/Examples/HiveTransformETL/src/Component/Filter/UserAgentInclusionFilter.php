<?php
namespace Examples\HiveTransformETL\Component\Filter;

use \Examples\HiveTransformETL\UserAgent\UserAgentInclusionCache;

/**
 * User agent inclusion filter object. Invokes the loader upon construction which ensures that the appropriate rules are loaded
 * into the instance. Will attempt to source an inclusion result from the cache; failing that, each rules is invoked against the
 * user agent value until a match occurs. If there is no match, the filter will return false
 * 
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
class UserAgentInclusionFilter implements IFieldValueFilter {
    /**
     * 
     * @var array
     */
    protected $rules;
    
    /**
     * Constructor
     * @codeCoverageIgnore
     */
    public function __construct() {
        Rules\Loader\FilterRuleLoader::load($this);
    }
    
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Component\Filter\IFieldValueFilter::filter()
     */
    public function filter($ua) {
        $cache = UserAgentInclusionCache::getInstance();
        
        // check if a result for this user agent is already in the cache
        if(is_null($pass = $cache->get($ua))) {
            // if there is no cached result, apply each filter to the user agent
            
            /* @var $rule \Examples\HiveTransformETL\Component\Filter\Rules\UserAgentWhitelistRule */
            for($rules = $this->getRules(), reset($rules), $pass = false;
                false === $pass && false !== ($rule = current($rules));
                next($rules)) {
    
                $pass = $rule->apply($ua);
            }
            
            $cache->add($ua, $pass); // cache the result of filtration
        }
        
        return $pass;
    }

    /**
     * Get the rules
     * @codeCoverageIgnore
     * @return \Examples\HiveTransformETL\Component\Filter\unknown
     */
    public function getRules() {
        return $this->rules;
    }
    
    /**
     * Set the rules
     * @codeCoverageIgnore
     * @param array $rules
     * @return \Examples\HiveTransformETL\Component\Filter\UserAgentInclusionFilter
     */
    public function setRules($rules) {
        $this->rules = $rules;
        return $this;
    }

    /**
     * Get an instance
     * 
     * @codeCoverageIgnore
     * @static
     * @return \Examples\HiveTransformETL\Component\Filter\UserAgentInclusionFilter
     */
    public static function instance() {
        return new static;
    }
}