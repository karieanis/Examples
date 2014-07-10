<?php
namespace Examples\ThriftServices\Hadoop\Conf;

/**
 * Base hadoop database conf class
 * @author Jeremy Rayner <jeremy@davros.com.au>
 * @codeCoverageIgnore
 */
abstract class HadoopDatabaseConf extends \Examples\ThriftServices\Conf\BaseConf {
    /**
     * Constructor
     * @codeCoverageIgnore
     */
    public final function __construct() {
        $this->applyThriftConf();
        $this->applyServiceConf();
    }
        
    /**
     * Apply thrift level configs to this config object
     * @return void
     */
    protected function applyThriftConf() {
        foreach($this->getThriftConf() as $key => $value) {
            $this[$key] = $value;
        }
    }
    
    /**
     * Apply service specific configs to this config object. This method should be
     * overriden by concrete service conf objects
     * @return void
     */
    protected function applyServiceConf() {
        
    }

    /**
     * Get the current thrift configuration
     * @final
     * @return \Examples\ThriftServices\Thrift\Conf\ThriftConf
     */
    protected final function getThriftConf() {
        return new \Examples\ThriftServices\Thrift\Conf\ThriftConf();
    }
    
    /**
     * Get an application config object. Entry point to reading yaml conf files
     * @final
     * @return \Director\Lib\Tools\Config
     * @codeCoverageIgnore
     */
    protected final function getConfig() {
        return new \Director\Lib\Tools\Config;
    }
}