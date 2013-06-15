<?php
namespace Examples\Impala\Conf;

/**
 * Imapala specific configuration business logic
 * @author Jeremy Rayner <jeremy@davros.com.au>
 * @codeCoverageIgnore
 */
class ImpalaConf extends \Examples\Hadoop\Conf\HadoopDatabaseConf {
    /**
     * @static
     * @var array
     */
    protected static $defaults = array(
        "auth_mechanism" => "NOSASL"
    );
    
    protected function applyDefaults() {
        foreach(static::$defaults as $key => $value) {
            $this[$key] = $value;
        }
    }
    
    protected function applyOverlay() {
        $conf = $this->getConfig();
        $thriftConfs = $conf::get("app>thrift");
        
        foreach($thriftConfs as $key => $value) {
            $this[$key] = $value;
        }
    }
    
    /**
     * Get an application config object. Entry point to reading yaml conf files
     * @codeCoverageIgnore
     * @return \Examples\Util\Config
     */
    protected function getConfig() {
        return new \Examples\Util\Config;
    }
}