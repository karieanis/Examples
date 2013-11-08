<?php
namespace Examples\HiveTransformETL\Model;

/**
 * Basic implementation of a data model consisting of both raw indexed data and a schema. Ensures that the object
 * can be utilised like a associative array by client code
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
abstract class AbstractSchemaRow implements \ArrayAccess, \Countable, \Iterator {
    /**
     * @var array
     */
    protected $data;
    /**
     * @var \Examples\HiveTransformETL\Schema\ISchemaMap
     */
    protected $schema;
    
    /**
     * Get the schema map
     * @return \Examples\HiveTransformETL\Schema\ISchemaMap
     */
    public function getSchema() {
        return $this->schema;
    }
    
    /**
     * Set the schema map
     * @param \Examples\HiveTransformETL\Schema\ISchemaMap $schema
     * @return \Examples\HiveTransformETL\Model\AbstractSchemaRow
     */
    final public function setSchema(\Examples\HiveTransformETL\Schema\ISchemaMap $schema) {
        $this->schema = $schema;
        return $this;
    }
    
    /**
     * Get the raw data
     * @return array
     */
    public function getData() {
        return $this->data;
    }
    
    /**
     * Set the raw data
     * @param array $data
     * @return \Examples\HiveTransformETL\Model\AbstractSchemaRow
     */
    final public function setData(array $data) {
        $this->data = $data;
        return $this;
    }

    /* (non-PHPdoc)
     * @see Iterator::current()
     */
    public function current() {
        return current($this->data);    
    }
    
    /* (non-PHPdoc)
     * @see Iterator::next()
     */
    public function next() {
        return next($this->data);
    }
    
    /* (non-PHPdoc)
     * @see Iterator::key()
     */
    public function key() {
        $key = key($this->data);
        return is_null($key) ? $key : $this->getSchema()->getNameAt($key);
    }
    
    /* (non-PHPdoc)
     * @see Iterator::valid()
     */
    public function valid() {
        return !is_null(key($this->data));
    }
    
    /* (non-PHPdoc)
     * @see Iterator::rewind()
     */
    public function rewind() {
        return reset($this->data);
    }
    
    /* (non-PHPdoc)
     * @see ArrayAccess::offsetExists()
     */
    public function offsetExists($offset) {
        if(false !== ($idx = $this->getSchema()->getIndexFor($offset))) {
            return isset($this->data[$idx]);
        }
        
        return false;
    }
    
    /* (non-PHPdoc)
     * @see ArrayAccess::offsetGet()
     */
    public function offsetGet($offset) {
        return $this->offsetExists($offset) ? $this->data[$this->getSchema()->getIndexFor($offset)] : null;
    }
    
    /* (non-PHPdoc)
     * @see ArrayAccess::offsetSet()
     */
    public function offsetSet($offset, $value) {
        $this->data[$this->getSchema()->getIndexFor($offset)] = $value;
    }
    
    /* (non-PHPdoc)
     * @see ArrayAccess::offsetUnset()
     */
    public function offsetUnset($offset) {
        throw \Examples\HiveTransformETL\Exception\OperationNotAllowedException("Unsetting of %s data is unsupported", get_class($this), 0);
    }
    
    /* (non-PHPdoc)
     * @see Countable::count()
     */
    public function count() {
        return count($this->data);
    }
    
    /**
     * Get a string representation of this object
     * @return mixed
     */
    public function __toString() {
        $output = array();
        
        for($i = 0, $s = $this->getSchema(), $d = $this->getData(); false !== ($index = $s->getNameAt($i)); $i++) {
            $output[$index] = isset($d[$i]) ? $d[$i] : NULL;
        }
        
        return var_export($output, true);
    }

    /**
     * Translate the raw data for this object to the passed schema
     * @param \Examples\HiveTransformETL\Schema\ISchemaMap $schema
     * @return \Examples\HiveTransformETL\Model\AbstractSchemaRow
     */
    public function toSchema(\Examples\HiveTransformETL\Schema\ISchemaMap $schema) {
        $data = array();
        
        for($i = 0; false !== ($key = $schema->getNameAt($i)); $i++) {
            $data[$i] = $this[$key];
        }
        
        return static::create($data, $schema);
    }
    
    /**
     * Get an instance
     * @return \Examples\HiveTransformETL\Model\AbstractSchemaRow
     */
    final protected static function instance() {
        return new static;
    }
    
    /**
     * Create a new instance, ensure that the raw data and schema is set
     * @param array $data
     * @param \Examples\HiveTransformETL\Schema\ISchemaMap $schema
     * @return \Examples\HiveTransformETL\Model\AbstractSchemaRow
     */
    public static function create($data, \Examples\HiveTransformETL\Schema\ISchemaMap $schema) {
        return self::instance()
            ->setData($data)
            ->setSchema($schema);
    }
}