<?php
namespace Examples\HiveTransformETL\Cache;

/**
 * Internal cache interface
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
interface ICache extends \Countable {
    /**
     * Add an item to the cache
     * @param mixed $key        The cache key
     * @param mixed $value      The value to be cached
     */
    public function add($key, $value);
    /**
     * Retrieve an item from the cache
     * @param mixed $key        The cache key
     * @return mixed            The cached value or null
     */
    public function get($key);
    /**
     * Remove an item from the cache
     * @param mixed $key        The cache key
     */
    public function remove($key);
    /**
     * Flush all of the items from the cache
     */
    public function flush();
}