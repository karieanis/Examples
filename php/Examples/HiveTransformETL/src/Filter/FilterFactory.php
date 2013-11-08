<?php
namespace Examples\HiveTransformETL\Filter;

/**
 * Basic factory class for generating filter instances
 * @final
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
final class FilterFactory {
    /**
     * Construct a new instance of filter
     * @param string $filterClass            The filter class name
     * @return mixed                         The manufactured filter object
     */
    public static function factory($filterClass) {
        return new $filterClass;
    }
}