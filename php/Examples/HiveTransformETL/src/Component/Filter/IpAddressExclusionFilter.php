<?php
namespace Examples\HiveTransformETL\Component\Filter;

use Examples\HiveTransformETL\Filter\IpAddressExclusionCache;

/**
 * Ip exclusion filter object. Invokes the loader upon construction which ensures the appropriate rules are loaded into the
 * instance. Will attempt to source an exclusion result from the cache; failing that, each rule is invoked against the ip
 * value until a match occurs. If there is no match, the filter will return false
 * 
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
class IpAddressExclusionFilter implements IFieldValueFilter {
    /**
     * @var array
     */
    protected $rules;
    
    /**
     * @codeCoverageIgnore
     * Constructor
     */
    public function __construct() {
        Rules\Loader\FilterRuleLoader::load($this); // load rules
    }
    
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Component\Filter\IFieldValueFilter::filter()
     */
    public function filter($ipAddress) {
        $cache = IpAddressExclusionCache::getInstance();
        
        // check if a result for this ip address is already in the cache
        if(is_null($filter = $cache->get($ipAddress))) {
            // if there is no cache result, apply each filter to the ip address 
            
            /* @var $rule Rules\RegexRule */
            for($rules = $this->getRules(), reset($rules), $filter = false;
                false === $filter && false !== ($rule = current($rules));
                next($rules)) {
    
                $filter = $rule->apply($ipAddress);
            }
            
            $cache->add($ipAddress, $filter); // cache the result of filtration
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
     * @return \Examples\HiveTransformETL\Component\Filter\IpAddressExclusionFilter
     */
    public function setRules($rules) {
        $this->rules = $rules;
        return $this;
    }
    
    /**
     * Get an instance
     * @codeCoverageIgnore
     * @return \Examples\HiveTransformETL\Component\Filter\IpAddressExclusionFilter
     */
    public static function instance() {
        return new static;
    }
}