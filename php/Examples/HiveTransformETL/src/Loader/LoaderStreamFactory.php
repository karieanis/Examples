<?php
namespace Examples\HiveTransformETL\Loader;

/**
 * Responsible for the manufacture of ILoadStreams
 * @final
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
final class LoaderStreamFactory {
    /**
     * Create a new ILoadStream
     * 
     * @static
     * @param string $LoaderStreamClass
     * @return \Examples\HiveTransformETL\Loader\ILoadStream
     */
    public static function factory($LoaderStreamClass) {
        return new $LoaderStreamClass;
    }
}