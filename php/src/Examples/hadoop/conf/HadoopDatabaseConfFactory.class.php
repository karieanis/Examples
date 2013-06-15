<?php
namespace Examples\Hadoop\Conf;

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
    protected static $baseConfClass = "\Examples\Hadoop\Conf\HadoopDatabaseConf";
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
    
    /**
     * Register the passed class string as the configuration class to use for generating HiveConf objects. Class
     * must extend from \Examples\Hadoop\Conf\HadoopDatabaseConf
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
        
        \Logger::getLogger('ThriftDatabaseLogger')->info(sprintf("Registering conf class %s", $confClass));
        static::$confClass = $confClass;
    }

    /**
     * Factory method used to generate an instance of the registered $confClass, throws an exception of an implementation
     * class has not been registered.
     * 
     * @throws HadoopDatabaseConfFactoryException
     * @return \Examples\Hadoop\Conf\HadoopDatabaseConf
     */
    public static function factory() {
        if(is_null(static::$confClass)) {
            throw new HadoopDatabaseConfFactoryException("Factory called without first registering a concrete conf class");
        }
        
        $instance = new static::$confClass;
        return $instance;
    }
}