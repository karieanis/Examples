<?php
namespace Examples\HiveTransformETL\Test\Util;

use \Examples\HiveTransformETL\Application\Conf\HiveTransformerConf,
    \Examples\HiveTransformETL\Application\ApplicationContext;

abstract class CacheUnitTest extends \PHPUnit_Framework_TestCase {
    /**
     * 
     * @var object
     */
    protected $cache;
    
    /**
     * Implement in concrete classes. Return an instance of the cache class to be tested
     */
    abstract protected function getCache();

    public static function setUpBeforeClass() {
        ApplicationContext::getInstance()->setConf($conf = new HiveTransformerConf());
    }
    
    public static function tearDownAfterClass() {
        $ctx = ApplicationContext::getInstance();
        unset($ctx);
    }
    
    protected function setUp() {
        $this->cache = $this->getCache();
    }
    
    protected function tearDown() {
        unset($this->cache);
    }
}