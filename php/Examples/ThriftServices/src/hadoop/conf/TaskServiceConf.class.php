<?php
namespace Examples\ThriftServices\Hadoop\Conf;

/**
 * @final
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
final class TaskServiceConf extends \Examples\ThriftServices\Conf\BaseConf {
    /**
     * Constructor
     */
    public function __construct() {
        $this->load();
    }
    
    /**
     * Load service information into this configuration object
     */
    protected function load() {
        $config = $this->getConfig();
        $this->vars = $config::get("task>services");
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
