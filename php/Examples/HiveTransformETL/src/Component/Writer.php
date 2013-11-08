<?php
namespace Examples\HiveTransformETL\Component;

/**
 * Stream based writer
 * @author Jeremy Rayner <jeremy@davros.com.au>
 * @codeCoverageIgnore
 *
 */
class Writer extends Streamable {
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
     * Open a write resource to the stream for this object
     */
    public function open() {
        $this->pointer = fopen($this->getStream(), "w");
    }
    
    /**
     * Write the passed output into the open stream
     * @param mixed $output
     */
    public function write($output) {
        if(is_null($this->pointer)) {
            $this->open();
        }
        
        // ensure new line is appended
        if(substr($output, -1, 1) !== PHP_EOL) {
            $output .= PHP_EOL;
        }
        
        fwrite($this->pointer, $output, strlen($output));
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