<?php
namespace Examples\ThriftServices\Thrift\Conf;

/**
 * @final
 * @author Jeremy Rayner <jeremy@davros.com.au>
 * @codeCoverageIgnore
 */
final class ThriftConf extends \Examples\ThriftServices\Conf\BaseConf {
    /**
     * Constructor
     */
    public function __construct() {
        $this->applyThriftConf();
    }
    
    /**
     * Apply the retrieved thrift conf values to this conf object
     * @return void
     */
    protected function applyThriftConf() {
        foreach($this->getThriftConf() as $key => $value) {
            $this[$key] = $value;
        }
    }

    /**
     * Application specific business logic, retrieve configs from the app yml.
     * @return mixed
     */
    protected function getThriftConf() {
        $conf = $this->getConfig();
        $ThriftConf = $conf::get("app>thrift");
        return $ThriftConf['config'];
    }
    
    /**
     * Get an application config object. Entry point to reading yaml conf files
     * @return \Director\Lib\Tools\Config
     */
    protected function getConfig() {
        return new \Director\Lib\Tools\Config;
    }
}