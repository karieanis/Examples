<?php
namespace Examples\ThriftServices\Hadoop\Conf;

/**
 * Abstract layer used for registering configuration implementation classes and manufacturing said instances.
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
final class HadoopDatabaseConfFactory {
    /**
     * Base class signiture for conf class implementations. Used for type checking in HadoopDatabaseConfFactory::register
     * @var string
     */
    protected static $baseConfClass = "\Examples\ThriftServices\Hadoop\Conf\HadoopDatabaseConf";
    /**
     * Name of the registered conf class which implements $baseConfClass
     * @var string
     */
    protected static $confClass;
    
    /**
     * Protected constructor, ensures that this is basically a static class
     * @codeCoverageIgnore
     */
    protected function __construct() {}
    
    protected static function getLogger() {
        return \Logger::getLogger('servicesLogger');
    }
    
    /**
     * Register the passed class string as the configuration class to use for generating configuration objects. Class
     * must extend from \Examples\ThriftServices\Hadoop\Conf\HadoopDatabaseConf
     * 
     * @param string $confClass
     * @throws \InvalidArgumentException
     * @throws \ReflectionException
     */
    public static function register($confClass) {
        try {
            $reflector = new \ReflectionClass($confClass);
            
            if(!$reflector->isSubclassOf(static::$baseConfClass)) {
                throw new \InvalidArgumentException(sprintf(
                        "%s does not extend from required base class %s",
                        $confClass,
                        static::$baseConfClass
                    )
                );
            }
        } catch(\Exception $e) { // could be a ReflectionException or InvalidArgumentException
            throw $e;
        }
        
        static::getLogger()->info(sprintf("Registering conf class %s", $confClass));
        static::$confClass = $confClass;
    }
    
    public static function deregister() {
        if(!is_null(static::$confClass)) {
            static::$confClass = null;
        }
    }

    /**
     * Factory method used to generate an instance of the registered $confClass, throws an exception of an implementation
     * class has not been registered.
     * 
     * @throws HadoopDatabaseConfFactoryException
     * @throws InvalidConfException
     * @return \Examples\ThriftServices\Hadoop\Conf\HadoopDatabaseConf
     */
    public static function factory() {
        if(is_null(static::$confClass)) {
            throw new HadoopDatabaseConfFactoryException("Factory called without first registering a concrete conf class");
        }
        
        $instance = new static::$confClass;
        $validator = \Examples\ThriftServices\Hadoop\Conf\Validator\HadoopDatabaseConfValidatorFactory::factory($instance);
        
        if(!$validator->validate($instance)) {
            throw new InvalidConfException(
                sprintf(
                    "Conf class %s is invalid. The following errors have been detected: %s",
                    get_class($instance), implode(", ", $validator->getErrors())        
                )        
            );
        }
        
        return $instance;
    }
    
    /**
     * 
     * @param string $ConfClass
     * @throws \InvalidArgumentException
     * @throws \Exception
     * @throws \InvalidConfException
     * @return \Examples\ThriftServices\Conf\BaseConf
     */
    public static function create($ConfClass) {
        try {
            $reflector = new \ReflectionClass($ConfClass);
        
            if(!$reflector->isSubclassOf(static::$baseConfClass)) {
                throw new \InvalidArgumentException(
                    sprintf("%s does not extend from required base class %s", $ConfClass, static::$baseConfClass)
                );
            }
        } catch(\Exception $e) { // could be a ReflectionException or InvalidArgumentException
            throw $e;
        }
        
        $instance = new $ConfClass;
        $validator = \Examples\ThriftServices\Hadoop\Conf\Validator\HadoopDatabaseConfValidatorFactory::factory($instance);
        
        if(!$validator->validate($instance)) {
            throw new InvalidConfException(
                sprintf(
                    "Conf class %s is invalid. The following errors have been detected: %s",
                    get_class($instance), implode(", ", $validator->getErrors())
                )
            );
        }
        
        static::getLogger()->info(sprintf("Conf object %s created", get_class($instance)));
        return $instance;
    }
}