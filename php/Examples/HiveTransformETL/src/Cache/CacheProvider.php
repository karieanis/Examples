<?php
namespace Examples\HiveTransformETL\Cache;

use \Examples\HiveTransformETL\Application\ApplicationContext, 
    \Examples\HiveTransformETL\Application\Conf\HiveTransformerConf;

/**
 * Abstraction layer for internal cache provisioning.
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
final class CacheProvider {
    /**
     * Get an internal cache instance
     * @param mixed $object    The object requesting a cache implementation
     */
    public static function get($object) {
        $conf = ApplicationContext::getInstance()->getConf();
        $factory = $conf->getCacheFactoryClass();

        return $factory::factory($conf->getCacheImplClass());
    }
}