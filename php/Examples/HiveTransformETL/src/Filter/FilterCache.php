<?php
namespace Examples\HiveTransformETL\Filter;

use \Examples\HiveTransformETL\Util\ReflectionUtils,
    \Examples\HiveTransformETL\Util\CacheUtils,
    \Examples\HiveTransformETL\Component\Logger\LoggerProvider,
    \Examples\HiveTransformETL\Cache\CacheProvider;

/**
 * Application level caching logic. Manages the caching of filter objects.
 * @final
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
final class FilterCache implements \Countable {
    /**
     * 
     * @staticvar \Examples\HiveTransformETL\Filter\FilterCache
     */
    protected static $instance;
    
    /**
     *
     * @var \Examples\HiveTransformETL\Cache\ICache
     */
    protected $cache;
    /**
     *
     * @var \Logger
     */
    protected $logger;
    
    /**
     * Construct
     */
    protected function __construct() {
        $this->logger = LoggerProvider::getLogger($this);
        $this->setCacheImpl(CacheProvider::get($this));
    }
    
    /**
     * Get the singleton
     * @return \Examples\HiveTransformETL\Filter\FilterCache
     */
    public static function getInstance() {
        if(!(self::$instance instanceof static)) {
            self::$instance = new static;
        }
    
        return self::$instance;
    }
    
    /**
     * Add the filter object to the cache. The cache util class will generate a cache key based off of the class name of the
     * filter object
     * 
     * @param mixed $filter
     */
    public function add($filter) {
        $name = ReflectionUtils::resolveClassName($filter);
        $key = CacheUtils::getCacheKey($name);
    
        $this->logger->debug(sprintf("Adding %s to %s", $name, get_class($this)));
        $this->getCacheImpl()->add($key, $filter);
        $this->logger->info(sprintf("%d item(s) in cache", count($this)));
    }
    
    /**
     * Attempt to retrieve a filter from the cache. If no result is found, null is returned
     * @param string $className
     * @return mixed
     */
    public function get($className) {
        $name = ReflectionUtils::resolveClassName($className);
        $key = CacheUtils::getCacheKey($name);
    
        $this->logger->debug(sprintf("Attempting to retrieve %s from %s using cache key %s", $name, get_class($this), $key));
        return $this->getCacheImpl()->get($key);
    }
    
    /**
     * Remove all filters for the passed class from the cache
     * @param string $className
     */
    public function remove($className) {
        $name = ReflectionUtils::resolveClassName($className);
        $key = CacheUtils::getCacheKey($name);
    
        $this->getCacheImpl()->remove($key);
        $this->logger->info(sprintf("%d item(s) in cache", count($this)));
    }
    
    /* (non-PHPdoc)
     * @see Countable::count()
     */
    public function count() {
        return count($this->getCacheImpl());
    }
    
    /**
     * Flush the internal cache
     */
    public function flush() {
        $this->getCacheImpl()->flush();
    }
    
    /**
     * Get the internal cache implementation
     * @return \Examples\HiveTransformETL\Cache\ICache
     */
    protected function getCacheImpl() {
        return $this->cache;
    }
    
    /**
     * Set the internal cache implementation
     * @param \Examples\HiveTransformETL\Cache\ICache $cache
     * @return \Examples\HiveTransformETL\Filter\FilterCache
     */
    protected function setCacheImpl(\Examples\HiveTransformETL\Cache\ICache $cache) {
        $this->cache = $cache;
        return $this;
    }
    
    /**
     * Destructor - ensures that the internal cache is flushed
     */
    public function __destruct() {
        $this->flush();
    }
}