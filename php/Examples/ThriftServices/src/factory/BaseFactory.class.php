<?php
namespace Examples\ThriftServices\Factory;

/**
 * 
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
abstract class BaseFactory implements IFactory {
    /**
     * 
     * @var \Logger
     */
    protected $logger;
    
    /**
     * @return array
     */
    abstract public function &getClassMap();
    
    /**
     * @see \Examples\ThriftServices\Factory\IFactory::register()
     * @throws \Examples\ThriftServices\Factory\AlreadyRegisteredException
     */
    public function register($key, $class) {
        $map = &$this->getClassMap();
        
        if(isset($map[$key])) {
            throw new \Examples\ThriftServices\Factory\AlreadyRegisteredException(
                sprintf(
                    "%s has already been registered with %s",
                    $class, get_class($this)
                )
            );
        }
        
        $this->getLogger()->info(sprintf("Registering %s as manufacturable with key %s", $class, $key));
        $map[$key] = $class;
    }
    
    /**
     * @see \Examples\ThriftServices\Factory\IFactory::create()
     * @throws \Examples\ThriftServices\Factory\NotRegisteredException
     */
    public function create($key) {
        $map = &$this->getClassMap();
        
        if(!isset($map[$key])) {
            throw new \Examples\ThriftServices\Factory\NotRegisteredException(
                sprintf(
                    "%s has not been registered with %s",
                    $key, get_class($this)
                )
            );
        }
        
        $Class = $map[$key];
        $this->getLogger()->info(sprintf("Attempting to instantiate new instance of %s", $Class));
        
        $Reflector = new \ReflectionClass($Class);
        $instance = $Reflector->newInstance();
        $this->postCreate($instance);
        
        return $instance;
    }

    /**
     * 
     * @return Logger
     */
    protected function getLogger() {
        return $this->logger;
    }
    
    /**
     * 
     * @param \Logger $logger
     * @return \Examples\ThriftServices\Factory\BaseFactory
     */
    protected function setLogger(\Logger $logger) {
        $this->logger = $logger;
        return $this;
    }
    
    /**
     * Override in concrete classes to provide specific business logic after instantiation
     * @param mixed $instance
     * @codeCoverageIgnore
     */
    protected function postCreate($instance) {
        
    }
}