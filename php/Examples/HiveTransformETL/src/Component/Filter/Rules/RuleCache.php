<?php
namespace Examples\HiveTransformETL\Component\Filter\Rules;

use \Examples\HiveTransformETL\Util\ReflectionUtils,
    \Examples\HiveTransformETL\Util\CacheUtils,
    \Examples\HiveTransformETL\Component\Logger\LoggerProvider,
    \Examples\HiveTransformETL\Cache\CacheProvider;

/**
 * Application level cachine logic. Manages the cache of logcal rules.
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
final class RuleCache implements \Countable {
    /**
     * 
     * @var \Examples\HiveTransformETL\Component\Filter\Rules\RuleCache
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
     * @codeCoverageIgnore
     */
    protected function __construct() { 
        $this->logger = LoggerProvider::getLogger($this);
        $this->setCacheImpl(CacheProvider::get($this));
    }
    
    /**
     * Get the singleton
     * @return \Examples\HiveTransformETL\Component\Filter\Rules\RuleCache
     */
    public static function getInstance() {
        if(!(self::$instance instanceof static)) {
            self::$instance = new static;
        }
        
        return self::$instance;
    }
    
    /**
     * Add rules to the cache. The cache util class will generate a cache key based off of the class name of the filter
     * object.
     * 
     * @param mixed $filter       A filter
     * @param mixed $rules        An array of IRule
     */
    public function add($filter, $rules) {
        $name = ReflectionUtils::resolveClassName($filter);
        $key = CacheUtils::getCacheKey($name);
        
        $this->logger->debug(sprintf("Adding rules for %s to cache", $name));
        $this->getCacheImpl()->add($key, $rules);
        $this->logger->info(sprintf("%d item(s) in cache", count($this)));
    }
    
    /**
     * Attempt to retrieve rules for the filter from the cache. If no rules are found, null is returned
     * @param mixed $filter
     * @return mixed
     */
    public function get($filter) {
        $name = ReflectionUtils::resolveClassName($filter);
        $key = CacheUtils::getCacheKey($name);
        
        $this->logger->debug(sprintf("Attempting to retrieve rules for %s using cache key %s", $name, $key));
        return $this->getCacheImpl()->get($key);
    }
    
    /**
     * Attempt to remove rules from the cache applicable to the passed filter
     * @param mixed $filter
     */
    public function remove($filter) {
        $name = ReflectionUtils::resolveClassName($className);
        $key = CacheUtils::getCacheKey($name);
        
        $this->getCacheImpl()->remove($key);
        $this->logger->info(sprintf("%d item(s) in cache", count($this)));
    }
    
    /**
     * Flush the internal cache
     */
    public function flush() {
        $this->getCacheImpl()->flush();
    }

    /* (non-PHPdoc)
     * @see Countable::count()
     */
    public function count() {
        return count($this->getCacheImpl());
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
     * @return \Examples\HiveTransformETL\Component\Filter\Rules\RuleCache
     */
    protected function setCacheImpl(\Examples\HiveTransformETL\Cache\ICache $cache) {
        $this->cache = $cache;
        return $this;
    }
    
    /**
     * Destructor
     */
    public function __destruct() {
        $this->flush();
    }
}