<?php
namespace Examples\HiveTransformETL\Cache;

/**
 * PHP memory cache implementation. Stores all key / value pairs in memory.
 * 
 * @final
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
final class PhpMemoryCache implements ICache {
    /**
     * @var array
     */
    protected $cache;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->flush();
    }
    
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Cache\ICache::add()
     */
    public function add($key, $value) {
        $this->cache[$key] = $value;
    }
    
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Cache\ICache::get()
     */
    public function get($key) {
        return isset($this->cache[$key]) ? $this->cache[$key] : null;
    }
    
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Cache\ICache::remove()
     */
    public function remove($key) {
        if(!is_null($this->get($key))) {
            unset($this->cache[$key]);
        }
    }
    
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Cache\ICache::flush()
     */
    public function flush() {
        $this->cache = array();
    }
    
    /* (non-PHPdoc)
     * @see Countable::count()
     */
    public function count() {
        return count($this->cache);
    }
    
    /**
     * Get an instance
     * @static
     * @return \Examples\HiveTransformETL\Cache\PhpMemoryCache
     */
    public static function instance() {
        return new static;
    }
}