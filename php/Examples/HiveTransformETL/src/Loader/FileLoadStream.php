<?php
namespace Examples\HiveTransformETL\Loader;

/**
 * Proxy to allow for the reading of OS files using the file stream wrapper
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
final class FileLoadStream implements ILoadStream {
    /**
     * 
     * @return \Examples\HiveTransformETL\Loader\FileLoadStream
     */
    public static function instance() {
        return new static;
    }
    
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Loader\ILoadStream::getStream()
     */
    public function getStream() {
        return "file://";
    }
    
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Loader\ILoadStream::getPath()
     */
    public function getPath($inPath) {
        return $this->getStream() . preg_replace(
                "@([a-zA-Z0-9])" . DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR . "([a-zA-Z0-9]+^" . DIRECTORY_SEPARATOR . ")?@", 
                "$1" . DIRECTORY_SEPARATOR . "$2", 
                $inPath
        );
    }
}