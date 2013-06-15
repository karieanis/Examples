<?php
namespace Examples\Hadoop\Conf;

/**
 * Base hadoop database conf class
 * @author Jeremy Rayner <jeremy@davros.com.au>
 * @codeCoverageIgnore
 */
abstract class HadoopDatabaseConf implements \ArrayAccess {
    /**
     * Container for configurations
     * @var array
     */
    protected $vars = array();
    
    /**
     * Constructor
     * @codeCoverageIgnore
     */
    public final function __construct() {
        $this->applyDefaults();
        $this->applyOverlay();
    }
    
    /**
     * 
     * @see ArrayAccess::offsetExists()
     */
    public function offsetExists($offset) {
        return isset($this->vars[$offset]);
    }
    
    /**
     * 
     * @see ArrayAccess::offsetGet()
     */
    public function offsetGet($offset) {
        return $this->offsetExists($offset) ? $this->vars[$offset] : null;
    }
    
    /**
     * 
     * @see ArrayAccess::offsetSet()
     */
    public function offsetSet($offset, $value) {
        $this->vars[$offset] = $value;
    }
    
    /**
     * 
     * @see ArrayAccess::offsetUnset()
     */
    public function offsetUnset($offset) {
        unset($this->vars[$offset]);
    }
    
    protected function applyDefaults() {
        
    }
    
    protected function applyOverlay() {
        
    }
}