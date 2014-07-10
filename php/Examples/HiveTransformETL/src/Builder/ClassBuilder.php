<?php
namespace Examples\HiveTransformETL\Builder;

use \Examples\HiveTransformETL\Component\Logger\LoggerProvider;

/**
 * Very basic builder implementation - construct objects, build arguments and set properties based on passed configs
 * @author Jeremy Rayner <jeremy@davros.com.au>
 */
class ClassBuilder {
    /**
     * 
     * @var \Examples\HiveTransformETL\Component\Logger\LoggerProvider
     */
    protected $logger;
    
    /**
     * Constructor
     * @codeCoverageIgnore
     */
    public function __construct() {
        $this->logger = LoggerProvider::getLogger($this);
    }
    
    /**
     * Generate an instance of the passed class name utilising the configuration array provided
     * @param string $className        The class to be built
     * @param array $config            The configuration
     * @return object                  An instance of the class
     */
    public function build($className, array $config) {
        $reflector = new \ReflectionClass($className);
        
        // if the class can be manufactured using a factory
        if(isset($config['factory']) && ($factoryCfg = $config['factory'])) {
            $factory = $factoryCfg['class'];     // get the factory class name
            $method  = $factoryCfg['method'];    // get the method to invoke

            try {
                $invoker = new \ReflectionMethod($factory, $method);
                $obj = $invoker->invoke(null, $className); // assumes the factory method is static
            } catch(\Exception $e) {
                // @codeCoverageIgnoreStart
                $this->logger->error($e->getMessage(), $e);
                throw $e;
                // @codeCoverageIgnoreEnd
            }
        } else {
            $args = array();
            
            // check if there are any arguments required for the constructor
            if(is_array($config) && isset($config['arguments'])) {
                foreach($config['arguments'] as $arg) {
                    if(is_array($arg) && isset($arg['class'])) {
                        // @codeCoverageIgnoreStart
                        $value = $this->build($arg['class'], $arg);    // if one of the arguments is an object, try to build it
                    } else { // @codeCoverageIgnoreEnd
                        $value = $arg;
                    }
            
                    $args[] = $value; // add the argument to the list of arguments
                }
            }
                        
            try {
                // this is for backwards compatibility - some versions of the reflection API don't like invocations with empty arguments
                if(count($args) > 0) {
                    $obj = $reflector->newInstanceArgs($args); // construct the new instance with the arguments provided
                } else {
                    $obj = $reflector->newInstance();
                }
            } catch(\ReflectionException $e) {
                // @codeCoverageIgnoreStart
                $this->logger->error($e->getMessage(), $e);
                throw $e;
                // @codeCoverageIgnoreEnd
            }
        }
        
        // this represents any post construction operations ( dependency injection )
        if(is_array($config) && isset($config['properties'])) {
            foreach($config['properties'] as $data) {
                if(is_array($data)) {
                    if(isset($data['class'])) {
                        $value = $this->build($data['class'], $data);
                    } else if(isset($data['value'])) {
                        $value = $data['value'];
                    }
                }

                if(isset($data['setter'])) {
                    $setter = $data['setter'];

                    /* @var $setterFn \ReflectionMethod */
                    try {
                        $setterFn = $reflector->getMethod($setter);
                        $setterFn->invoke($obj, $value);
                    } catch(\Exception $e) {
                        // @codeCoverageIgnoreStart
                        throw new \Exception(sprintf("Unable to invoke setter %s on %s", (string)$setter, (string)get_class($obj)), 0, $e);
                        // @codeCoverageIgnoreEnd
                    }
                }
            }
        }
        
        return $obj;
    }
    
    /**
     * Get an instance
     * @static
     * @return \Examples\HiveTransformETL\Builder\ClassBuilder
     */
    public static function instance() {
        return new static;
    }
}