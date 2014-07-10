<?php
namespace Examples\HiveTransformETL\Loader\Proxy;

use Examples\HiveTransformETL\Loader\ILoadStream,
    Examples\HiveTransformETL\Loader\ZipLoadStream,
    Examples\HiveTransformETL\Loader\FileLoadStream,
    Examples\HiveTransformETL\Exception\FileSystemException;

/**
 * Proxy to allow zip streams to be treated as file streams
 * @author Jeremy Rayner <jeremy@davros.com.au>
 * @codeCoverageIgnore
 */
final class ZipStreamToFileStreamProxy extends ZipStreamToLocalProxy {
    /**
     * @var FileLoadStream
     */
    protected $fileStream;
    
    /**
     * @param ILoadStream $inStream
     */
    public function __construct(ILoadStream $inStream) {
        $this->fileStream = FileLoadStream::instance();
        parent::__construct($inStream);
    }
    
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Loader\Proxy\ZipStreamToLocalProxy::getStream()
     */
    public function getStream() {
        return $this->fileStream->getStream();
    }
    
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Loader\Proxy\ZipStreamToLocalProxy::getPath()
     */
    public function getPath($inPath) {
        return $this->fileStream->getPath(parent::getPath($inPath));
    }
}