<?php
namespace Examples\HiveTransformETL\Component;

use \Examples\HiveTransformETL\Exception\StreamReadException;

/**
 * Stream based reader
 * @author Jeremy Rayner <jeremy@davros.com.au>
 * @codeCoverageIgnore
 */
class Reader extends Streamable implements IReader {
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
     * @throws StreamReadException
     */
    public function open() {
        if(!($this->pointer = @fopen($this->getStream(), "r"))) {
            throw new StreamReadException(
                    "Unable to open stream " . $this->getStream() . " for read operations",
                    0
            );
        }
    }
    
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Component\IReader::read()
     */
    public function read() {
        if(is_null($this->pointer)) {
           $this->open();
        }
        
        return stream_get_line($this->pointer, 65535, PHP_EOL);
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