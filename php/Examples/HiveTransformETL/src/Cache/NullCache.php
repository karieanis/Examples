<?php
namespace Examples\HiveTransformETL\Cache;

/**
 * Null cache implementation - ensures that nothing is ever cached
 * @author Jeremy Rayner <jeremy@davros.com.au>
 * @codeCoverageIgnore
 */
class NullCache implements ICache {
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Cache\ICache::add()
     */
    public function add($key, $value) {
        return;
    }
    
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Cache\ICache::get()
     */
    public function get($key) {
        return null;
    }
    
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Cache\ICache::remove()
     */
    public function remove($key) {
        return;
    }
    
    /* (non-PHPdoc)
     * @see Countable::count()
     */
    public function count() {
        return 0;
    }
    
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Cache\ICache::flush()
     */
    public function flush() {
        return;
    }

    /**
     * Get an instance
     * @static
     * @return \Examples\HiveTransformETL\Cache\NullCache
     */
    public static function instance() {
        return new static;
    }
}