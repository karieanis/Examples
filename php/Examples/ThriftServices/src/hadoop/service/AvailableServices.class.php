<?php
namespace Examples\ThriftServices\Hadoop\Service;

/**
 * 
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
final class AvailableServices {
    /**
     * 
     * @staticvar AvailableServices
     */
    protected static $instance;
    
    /**
     * 
     * @var string
     */
    protected $default;
    /**
     * 
     * @var array
     */
    protected $services = array();
    
    /**
     * Protected constructor, prevent direct instantiation
     */
    protected function __construct() {
        $this->load();
    }
    
    /**
     * @return \Examples\ThriftServices\Hadoop\Service\AvailableServices
     */
    public static function getInstance() {
        if(!(static::$instance instanceof static)) {
            static::$instance = new static();
        }
        
        return static::$instance;
    }
    
    /**
     * 
     * @return array
     */
    public function getServices() {
        return $this->services;
    }
    
    /**
     * 
     * @param string $key
     * @throws ServiceAvailabilityException
     * @return string
     */
    public function getService($key) {
        if(!isset($this->services[$key])) {
            $config = $this->getConfig();
            throw new ServiceAvailabilityException(
                sprintf("The requested service %s is not available for %s environment", $key, $config::env())
            );
        }
        
        return $this->services[$key];
    }
    
    public function getKeyByServiceClass($class) {
        return array_search($class, $this->services);
    }
    
    /**
     * @return string
     */
    public function getDefaultService() {
        return $this->default;
    }
    
    /**
     * 
     * @throws ServiceAvailabilityException
     */
    protected function load() {
        $config = $this->getConfig();
        $thrift = $config::get("app>thrift");
        
        if(!isset($thrift['default_service'])) {
            trigger_error(
                sprintf("No default service has been set for %s environment", $config::env()),
                E_USER_WARNING
            );
        } else {
            $this->default = $thrift['default_service'];
        }
        
        if(!isset($thrift['services'])) {
            throw new ServiceAvailabilityException(
                sprintf("No services available for %s environment!", $config::env())
            );
        }
        
        foreach($thrift['services'] as $ServiceKey => $ServiceConf) {
            $this->services = array_merge($this->services, array($ServiceKey => $ServiceConf['class']));
        }  
    }
    
    /**
     * 
     * @return \Director\Lib\Tools\Config
     */
    protected function getConfig() {
        return new \Director\Lib\Tools\Config;
    }
}