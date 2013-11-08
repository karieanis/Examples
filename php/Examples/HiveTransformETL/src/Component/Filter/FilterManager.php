<?php
namespace Examples\HiveTransformETL\Component\Filter;

use \Examples\HiveTransformETL\Util\Composite;

/**
 * Filter composite. Used to obsufucate the number of filters from the client code - all filters need to return false for
 * the row to be processed.
 * 
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
class FilterManager extends Composite implements IRowFilter {
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Component\Filter\IRowFilter::filter()
     */
    public function filter($row) {
        /* @var $filterObj IRowFilter */
        for($this->rewind(), $filter = false; !$filter && (false !== ($filterObj = $this->current())); $this->next()) {
            $filter = $filterObj->filter($row);
        }
        
        return $filter;
    }
}