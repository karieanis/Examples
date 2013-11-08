<?php
namespace Examples\ThriftServices\Utils;

/**
 * This class reads data from yml file and store them into arrays
 *
 * It supports cascading configurations, which means the values for default key 'all'
 * can be overridden by the environment variables
 */
class Config {
    protected static $data = array();

    /**
     * @static
     * @param $key
     * @return array
     */
    public static function get($key) {
        $keySplit = explode('>', $key);

        if(count($keySplit) != 2) {
            return null;
        }

        $env = static::env();
        $main = $keySplit[0];
        $sub = $keySplit[1];

        if(isset(static::$data[$main][$env][$sub])) {
            return static::$data[$main][$env][$sub];
        }

        return isset(static::$data[$main]['default'][$sub]) ? static::$data[$main]['default'][$sub] : null;
    }

    /**
     * Simple setter for overriding existing values with configs for specific BL (so far only used
     * for unit testing purposes)
     * 
     * @param string $key
     * @param mixed $value
     * @param string $env
     * @return boolean
     */
    public static function set($key, $value, $env = 'default') {
        $keySplit = explode('>', $key);
        
        if(count($keySplit) != 2) {
            return false;
        }
        
        $main = $keySplit[0];
        $sub = $keySplit[1];
        
        static::$data[$main][$env][$sub] = $value;
        return true;
    }
    
    /**
     * Return all configs
     *
     * @static
     * @return array
     * @codeCoverageIgnore
     */
    public static function getAll() {
        return static::$data;
    }

    public static function env($env='dev') {
        return defined('ENV') ? strtolower(ENV) : $env;
    }
}
