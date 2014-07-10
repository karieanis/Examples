<?php
namespace Examples\HiveTransformETL\Loader\Proxy;

use Examples\HiveTransformETL\Loader\ILoadStream,
    Examples\HiveTransformETL\Loader\ZipLoadStream,
    Examples\HiveTransformETL\Exception\FileSystemException;

/**
 * Proxy to allow zip streams to work like regular OS files
 * @author Jeremy Rayner <jeremy@davros.com.au>
 * @codeCoverageIgnore
 */
class ZipStreamToLocalProxy implements ILoadStream {
    /**
     * @var ZipLoadStream
     */
    protected $loadStream;
    
    /**
     * @var array
     */
    protected $tempFileRegistry;
    
    /**
     * @param ILoadStream $inStream
     */
    public function __construct(ILoadStream $inStream) {
        $this->loadStream       = $inStream;
        $this->tempFileRegistry = array();
        
        register_shutdown_function(array($this, 'shutdown'));
    }
    
    /**
     * @param ILoadStream $inStream
     * @return \Examples\HiveTransformETL\Loader\Proxy\ZipStreamToLocalProxy
     */
    public static function instance(ILoadStream $inStream) {
        return new static($inStream);
    }
    
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Loader\ILoadStream::getStream()
     */
    public function getStream() {
        return "";
    }
    
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Loader\ILoadStream::getPath()
     */
    public function getPath($inPath) {
        if($filename = tempnam("/tmp/", "hive-transform-etl")) {
            if($h = fopen($filename, "w")) {
                fwrite($h, $c = file_get_contents($this->loadStream->getPath($inPath)), strlen($c));
                fclose($h);
            } else {
                throw new FileSystemException(sprintf("Unable to write to temp file %s", $filename), 0);
            }
        } else {
            throw new FileSystemException("Unable to create temp file", 0);
        }
        
        chmod($filename, 0755);
        $this->tempFileRegistry[] = $filename;
        return $filename;
    }
    
    /**
     * Cleans up temporary files on shutdown
     */
    public function shutdown() {
        foreach($this->tempFileRegistry as $filename) {
            if(file_exists($filename)) {
                unlink($filename);
            }
        }
    }
}