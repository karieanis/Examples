<?php
namespace Examples\HiveTransformETL\Test\Unit\Component\Cache;

class PhpMemoryCacheTest extends \PHPUnit_Framework_TestCase {
    public function testCaching() {
        $cache = \Examples\HiveTransformETL\Cache\PhpMemoryCache::instance();
        $cache->add("test", "value");
        $this->assertEquals("value", $cache->get("test"));
        $this->assertCount(1, $cache);
        $cache->remove("test");
        $this->assertNull($cache->get("test"));
    }
}

return __NAMESPACE__;