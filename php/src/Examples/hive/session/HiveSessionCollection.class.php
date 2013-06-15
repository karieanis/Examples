<?php
namespace Examples\Hive\Session;

/**
 * Singleton HiveServer2 session manager. Wraps around a Collection object, and delegates all unknown method calls to
 * that underlying collection object (contains n amount of current sessions)
 * 
 * @author Jeremy Rayner <jeremy@davros.com.au>
 * @codeCoverageIgnore
 */
class HiveSessionCollection {
    /**
     * The current instance
     * @var HiveSessionCollection
     */
    protected static $instance;
    /**
     * The underlying collection object
     * @var \Director\Lib\Util\Collection
     */
    protected $collection;
    /**
     * Reflection class used to invoke calls on the underlying collection object
     * @var \ReflectionClass
     */
    protected $reflector;

    /**
     * Constructor
     */
    protected function __construct() {
        $this->collection = new \Director\Lib\Util\Collection();
        $this->reflector = new \ReflectionClass($this->collection);
    }
    
    /**
     * Get the current session collection instance
     * @static
     * @return HiveSessionCollection
     */
    public static function getInstance() {
        if(!(static::$instance instanceof static)) {
            static::$instance = new static();
        }
        
        return static::$instance;
    }
    
    /**
     * Invoke the passed method on the collection
     * 
     * @param string $method
     * @param array $args
     * @throws Exception
     * @return mixed
     */
    public function __call($method, $args) {
        try {
            $invoker = $this->reflector->getMethod($method);
            return $invoker->invokeArgs($this->collection, $args);
        } catch(\Exception $e) {
            throw $e;
        }
    }
}