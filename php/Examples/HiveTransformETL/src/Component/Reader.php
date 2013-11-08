<?php
namespace Examples\HiveTransformETL\Component;

/**
 * Stream based reader
 * @author Jeremy Rayner <jeremy@davros.com.au>
 * @codeCoverageIgnore
 */
class Reader extends Streamable {
    /**
     * @var resource
     */
    protected $pointer;
    
    /**
     * Constructor
     * @param string $stream
     */
    public function __construct($stream) {
        $this->setStream($stream);
    }

    /**
     * Open a read resource to the stream for this object
     */
    public function open() {
        $this->pointer = fopen($this->getStream(), "r");
    }
    
    /**
     * Read from the open stream
     * @return string
     */
    public function read() {
        if(is_null($this->pointer)) {
           $this->open();
        }
        
        return stream_get_line($this->pointer, 8192, PHP_EOL);
    }
    
    /**
     * Ensure the stream is closed
     */
    public function __destruct() {
        if(!is_null($this->pointer)) {
            fclose($this->pointer);
        }
    }
}