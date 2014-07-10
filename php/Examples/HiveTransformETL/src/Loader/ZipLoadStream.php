<?php
namespace Examples\HiveTransformETL\Loader;

/**
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
final class ZipLoadStream implements ILoadStream {
    /**
     * 
     * @return \Examples\HiveTransformETL\Loader\ZipLoadStream
     */
    public static function instance() {
        return new static;
    }
    
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Loader\ILoadStream::getStream()
     */
    public function getStream() {
        return "zip://";
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