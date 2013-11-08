<?php
namespace Examples\HiveTransformETL\Component\Filter;

use \Examples\HiveTransformETL\UserAgent\UserAgentExclusionCache;

/**
 * User agent exclusion filter object. Invokes the loader upon construction which ensures that the appropriate rules are loaded
 * into the instance. Will attempt to source an exclusion result from the cache; failing that, each rule is invoked against the
 * user agent value until a match occurs. If there is no match, the filter will return false
 * 
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
class UserAgentExclusionFilter implements IFieldValueFilter {
    /**
     * 
     * @var array
     */
    protected $rules;
    
    /**
     * Constrcutor
     * @codeCoverageIgnore
     */
    public function __construct() {
        Rules\Loader\FilterRuleLoader::load($this);
    }
    
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Component\Filter\IFieldValueFilter::filter()
     */
    public function filter($ua) {
        $cache = UserAgentExclusionCache::getInstance();
        
        // check if a result for this user agent is already in the cache
        if(is_null($filter = $cache->get($ua))) {
            // if there is no cached result, apply each filter to the user agent
            
            /* @var $rule \Examples\HiveTransformETL\Component\Filter\Rules\UserAgentBlacklistRule */
            for($rules = $this->getRules(), reset($rules), $filter = false;
                false === $filter && false !== ($rule = current($rules));
                next($rules)) {
                    
                $filter = $rule->apply($ua);
            }

            $cache->add($ua, $filter); // cache the result of filtration
        }
        
        return $filter;
    }
    
    /**
     * Get the rules
     * @codeCoverageIgnore
     * @return array
     */
    public function getRules() {
        return $this->rules;
    }
    
    /**
     * Set the rules
     * @codeCoverageIgnore
     * @param array $rules
     * @return \Examples\HiveTransformETL\Component\Filter\UserAgentExclusionFilter
     */
    public function setRules($rules) {
        $this->rules = $rules;
        return $this;
    }
    
    /**
     * Get an instance
     * @codeCoverageIgnore
     * @static
     * @return \Examples\HiveTransformETL\Component\Filter\UserAgentExclusionFilter
     */
    public static function instance() {
        return new static;
    }
}