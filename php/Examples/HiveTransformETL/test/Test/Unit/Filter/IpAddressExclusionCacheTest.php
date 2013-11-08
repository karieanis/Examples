<?php
namespace Examples\HiveTransformETL\Test\Unit\Filter;

use \Examples\HiveTransformETL\Test\Util\CacheUnitTest,
    \Examples\HiveTransformETL\Filter\IpAddressExclusionCache;

/**
 * 
 * @author Jeremy Rayner <jeremy@davros.com.au>
 * @runTestsInSeparateProcesses
 *
 */
class IpAddressExclusionCacheTest extends CacheUnitTest {
    /**
     * @return \Examples\HiveTransformETL\Filter\IpAddressExclusionCache
     */
    protected function getCache() {
        return IpAddressExclusionCache::getInstance();
    }
    
    public function testAdd() {
        $this->cache->add("192.168.0.1", true);
        $this->assertCount(1, $this->cache);
        $this->assertTrue($this->cache->get("192.168.0.1"));
    }
    
    public function testAddWithException() {
        $cache = clone $this->cache;
        
        $setter = new \ReflectionMethod($cache, "setCacheImpl");
        $setter->setAccessible(true);
        
        $cacheImpl = $this->getMockBuilder("\Examples\HiveTransformETL\Cache\NullCache")
            ->disableOriginalConstructor()
            ->setMethods(array('add'))
            ->getMock();
        
        $cacheImpl->expects($this->once())
            ->method('add')
            ->will($this->throwException(new \Exception("Fake exception for testing", 0)));
        
        $setter->invoke($cache, $cacheImpl);
        $cache->add("192.168.0.1", true);
    }
    
    public function testRemove() {
        $this->cache->add("192.168.0.1", true);
        $this->assertCount(1, $this->cache);
        $this->cache->remove("192.168.0.1");
        $this->assertCount(0, $this->cache);
    }
    
    public function testFlush() {
        $this->cache->add("192.168.0.1", true);
        $this->assertCount(1, $this->cache);
        $this->cache->flush();
        $this->assertCount(0, $this->cache);
    }
}

return __NAMESPACE__;