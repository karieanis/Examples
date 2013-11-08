<?php
namespace Examples\ThriftServices\Hive\Conf;

/**
 * Contains business logic specific to Effective Measure hive configurations
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
class HiveConf extends \Examples\ThriftServices\Hadoop\Conf\HadoopDatabaseConf {
    /**
     * @static
     * @var array
     */
    protected static $defaults = array(
        "auth_mechanism" => "NOSASL"        
    );
    
    /**
     * Apply all default configurations to this conf object
     * @return void
     */
    protected function applyDefaults() {
        foreach(static::$defaults as $key => $value) {
            $this[$key] = $value;
        }
    }
    
    /**
     * This is where the EM specific BL lives. Read the thrift configs from the required app yaml files, then
     * apply the results to this conf object
     * @return void
     */
    protected function applyOverlay() {
        $emConf = $this->getConfig();
        $thriftConfs = $emConf::get("app>thrift");
        
        foreach($thriftConfs as $key => $value) {
            $this[$key] = $value;
        }
    }
    
    /**
     * Get an application config object. Entry point to reading yaml conf files
     * @codeCoverageIgnore
     * @return \Examples\ThriftServices\Util\Config
     */
    protected function getConfig() {
        return new \Examples\ThriftServices\Util\Config;
    }
}