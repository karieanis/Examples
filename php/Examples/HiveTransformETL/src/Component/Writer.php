<?php
namespace Examples\HiveTransformETL\Component;

use \Examples\HiveTransformETL\Exception\StreamWriteException;

/**
 * Stream based writer
 * @author Jeremy Rayner <jeremy@davros.com.au>
 * @codeCoverageIgnore
 *
 */
class Writer extends Streamable implements IWriter {
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
     * @throws StreamWriteException
     */
    public function open() {
        if(!($this->pointer = @fopen($this->getStream(), "w"))) {
            throw new StreamWriteException(
                    "Unable to open stream " . $this->getStream() . " for write operations",
                    0        
            );
        }
    }
    
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Component\IWriter::write()
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