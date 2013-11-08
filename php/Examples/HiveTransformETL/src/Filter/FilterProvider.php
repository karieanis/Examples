<?php
namespace Examples\HiveTransformETL\Filter;

/**
 * Encapsulates the management of caching and manufacturing logic for filter objects
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
class FilterProvider {
    /**
     * Get an instance of the requested filter - if it's in the cache, use that, or else get the factory to construct one,
     * cache it, then return the instance to the client code
     * 
     * @static
     * @param string $className
     * @return mixed
     */
    public static function get($className) {
        $cache = FilterCache::getInstance();
        
        if(is_null($filter = $cache->get($className))) {
            $filter = FilterFactory::factory($className);
            $cache->add($filter);
        }
        
        return $filter;
    }
}