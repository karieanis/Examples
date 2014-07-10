<?php
namespace Examples\HiveTransformETL\Component;

use Examples\HiveTransformETL\Buffer\IBuffer;

/**
 * Wraps another IWriter object for the purpose of buffering output. Once the flush interval has been breached, flush everything in
 * the buffer through to wrapped IWriter.
 * 
 * @final
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
final class BufferedStreamWriter implements IWriter {
    /**
     * 
     * @var IBuffer
     */
    protected $buffer;
    /**
     * 
     * @var number
     */
    protected $flushInterval;
    /**
     * 
     * @var IWriter
     */
    protected $innerWriter;
    /**
     * 
     * @var number
     */
    protected $counter;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->counter = 0;
        register_shutdown_function(array($this, 'shutdown'));
    }
    
    /**
     * 
     * @return \Examples\HiveTransformETL\Component\BufferedStreamWriter
     */
    public static function instance() {
        return new static;
    }
    
    /**
     * Flush everything in the buffer to the inner writer
     */
    public function flush() {
        if(!is_null($this->innerWriter) && !is_null($this->buffer)) {
            $buffer = $this->getBuffer();
            
            if(!!$buffer->available()) {
                $this->getInnerWriter()->write(
                    $buffer->read($buffer->available())
                );
            }
        }
    }
    
    /**
     * @return \Examples\HiveTransformETL\Component\IWriter
     */
    public function getInnerWriter() {
        return $this->innerWriter;
    }
    
    /**
     * @param IWriter $w
     * @return \Examples\HiveTransformETL\Component\BufferedStreamWriter
     */
    public function setInnerWriter(IWriter $w) {
        $this->innerWriter = $w;
        return $this;
    }
    
    /**
     * @return number
     */
    public function getFlushInterval() {
        return $this->flushInterval;
    }
    
    /**
     * @param number $interval
     * @return \Examples\HiveTransformETL\Component\BufferedStreamWriter
     */
    public function setFlushInterval($interval) {
        $this->flushInterval = $interval;
        return $this;
    }

    /**
     * 
     * @return \Examples\HiveTransformETL\Buffer\IBuffer
     */
    public function getBuffer() {
        return $this->buffer;
    }
    
    /**
     * 
     * @param IBuffer $buffer
     * @return \Examples\HiveTransformETL\Component\BufferedStreamWriter
     */
    public function setBuffer(IBuffer $buffer) {
        $this->buffer = $buffer;
        return $this;
    }
    
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Component\IWriter::write()
     */
    public function write($output) {
        if(is_null($this->innerWriter)) {
            throw new \RuntimeException("Internal writer has not been set, unable to write", 0);
        }
        
        if(is_null($this->buffer)) {
            throw new \RuntimeException("Buffer implementation has not been set, unable to write to buffer", 0);
        }
        
        $buffer = $this->getBuffer();
        $buffer->write($output);
        $this->counter++;
        
        if(!!($this->counter % $this->getFlushInterval())) {
            $buffer->write(PHP_EOL); // append EOL to buffer
        } else {
            $this->flush(); // inner writer will append EOL to the last appended item
        }
    }
    
    /**
     * Ensure that all unflushed information is flushed
     */
    public function shutdown() {
        $this->flush();
    }
}