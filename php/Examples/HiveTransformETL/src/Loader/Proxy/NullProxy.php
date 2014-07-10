<?php
namespace Examples\HiveTransformETL\Loader\Proxy;

use Examples\HiveTransformETL\Loader\ILoadStream;

/**
 * Null proxy, passes all operations through to the inner stream
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
final class NullProxy implements ILoadStream {
    /**
     * @var ILoadStream
     */
    protected $loadStream;
    
    /**
     * @param ILoadStream $inStream
     */
    public function __construct(ILoadStream $inStream) {
        $this->loadStream = $inStream;
    }
    
    /**
     * @param ILoadStream $inStream
     * @return \Examples\HiveTransformETL\Loader\Proxy\NullProxy
     */
    public static function instance(ILoadStream $inStream) {
        return new static($inStream);
    }
    
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Loader\ILoadStream::getStream()
     */
    public function getStream() {
        return $this->loadStream->getStream();
    }
    
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Loader\ILoadStream::getPath()
     */
    public function getPath($inPath) {
        return $this->loadStream->getPath($inPath);
    }
}