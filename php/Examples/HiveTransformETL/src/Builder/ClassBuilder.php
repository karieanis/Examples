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
            
            $this->logger->debug(sprintf("Create object %s using factory method %s", $className, $factory . "::" . $method));
            
            try {
                $invoker = new \ReflectionMethod($factory, $method);
                $obj = $invoker->invoke(null, $className); // assumes the factory method is static
            } catch(\ReflectionException $e) {
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
            
                    array_push($args, $value); // add the argument to the list of arguments
                }
            }
            
            $this->logger->debug(sprintf("Constructing object %s using reflection", $className));
            
            try {
                $obj = $reflector->newInstanceArgs($args); // construct the new instance with the arguments provided
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
                    
                    $this->logger->debug(sprintf("Invoking setter %s on %s with arguments %s", $setter, $className, var_export($value, true)));
                    
                    /* @var $setterFn \ReflectionMethod */
                    try {
                        $setterFn = $reflector->getMethod($setter);
                        $setterFn->invoke($obj, $value);
                    } catch(\Exception $e) {
                        // @codeCoverageIgnoreStart
                        throw new \Exception(sprintf("Unable to invoke setter %s on %s", $setter, get_class($obj)), 0, $e);
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