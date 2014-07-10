<?php
namespace Examples\HiveTransformETL\Buffer;

/**
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
class MemoryBuffer implements IBuffer {
    /**
     * @var string
     */
    protected $buffer;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->buffer = "";
    }
    
    /**
     * 
     * @return \Examples\HiveTransformETL\Buffer\MemoryBuffer
     */
    public static function instance() {
        return new static;
    }
    
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Buffer\IBuffer::write()
     */
    public function write($input) {
        $this->buffer .= $input;
    }
    
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Buffer\IBuffer::read()
     */
    public function read($len) {
        if(!$this->available()) {
            throw new EmptyBufferException("Unable to read " . $len . " bytes from the buffer", 0);
        }
        
        if(($actualLength = strlen($this->buffer)) && $actualLength <= $len) {
            $len = $actualLength;
        }
        
        $output = substr($this->buffer, 0, $len);
        $this->buffer = substr($this->buffer, $len);
        
        return $output;
    }
    
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Buffer\IBuffer::available()
     */
    public function available() {
        return strlen($this->buffer);
    }
}