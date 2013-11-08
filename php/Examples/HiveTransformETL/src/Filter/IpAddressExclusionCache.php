<?php
namespace Examples\HiveTransformETL\Filter;

use \Examples\HiveTransformETL\Util\LanguageUtils,
    \Examples\HiveTransformETL\Component\Logger\LoggerProvider,
    \Examples\HiveTransformETL\Cache\CacheProvider;

/**
 * Application level caching logic. Manages the caching of ip address exclusion records.
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
class IpAddressExclusionCache implements \Countable {

    /**
     * 
     * @staticvar \Examples\HiveTransformETL\Filter\IpAddressExclusionCache
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
     * 
     */
    protected function __construct() {
        $this->logger = LoggerProvider::getLogger($this);
        $this->setCacheImpl(CacheProvider::get($this));
    }
    
    /**
     * Get the singleton
     * @return \Examples\HiveTransformETL\Filter\IpAddressExclusionCache
     */
    public static function getInstance() {
        if(!(self::$instance instanceof static)) {
            self::$instance = new static;
        }
    
        return self::$instance;
    }
    
    /**
     * Add a result into the cache - the ip address will be used as the key, with the value being the result of exclusion
     * matching (true or false)
     * 
     * @param boolean $ip
     * @param boolean $match
     */
    public function add($ip, $match) {
        
        try {
            $this->logger->debug(
                sprintf(
                    "Adding exclusion match record of %s for ip %s", 
                    (string)LanguageUtils::booleanToString($match), 
                    (string)$ip
                )
            );
            
            $this->getCacheImpl()->add($ip, $match);
            $this->logger->info(sprintf("%d item(s) in cache", (int)count($this)));
        } catch(\Exception $e) {
            LoggerProvider::getErrorLogger()->error($e->getMessage(), $e);
        }
    }

    /**
     * Attempt to result for exclusion matching for the passed ip form the cache. If no match is found, null is returned
     * @param string $ip
     * @return mixed
     */
    public function get($ip) {
        $this->logger->debug(sprintf("Attempting to retrieve data for ip %s", $ip));
        return $this->getCacheImpl()->get($ip);
    }
    
    /**
     * Remove any cached information for the passed ip
     * @param string $ip
     */
    public function remove($ip) {
        $this->getCacheImpl()->remove($ip);
    }
    
    /* (non-PHPdoc)
     * @see Countable::count()
     */
    public function count() {
        return count($this->getCacheImpl());
    }
    
    /**
     * Flush all records from the internal cache
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
     * @return \Examples\HiveTransformETL\Filter\IpAddressExclusionCache
     */
    protected function setCacheImpl(\Examples\HiveTransformETL\Cache\ICache $cache) {
        $this->cache = $cache;
        return $this;
    }
    
    /**
     * Destructor - ensures all cached records are flushed
     */
    public function __destruct() {
        $this->flush();
    }
}