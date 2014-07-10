<?php
namespace Examples\HiveTransformETL\Loader;

/**
 * Describes the methods required for stream based loading
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
interface ILoadStream {
    /**
     * @return string
     */
    public function getStream();
    /**
     * @param string $inPath
     * @return string
     */
    public function getPath($inPath);
}