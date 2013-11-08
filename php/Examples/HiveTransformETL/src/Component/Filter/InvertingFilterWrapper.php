<?php
namespace Examples\HiveTransformETL\Component\Filter;

/**
 * Used the invert the result of the inner filter
 * 
 * @final
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
final class InvertingFilterWrapper implements IRowFilter {
    /**
     * @var IRowFilter
     */
    protected $innerFilter;
    
    /**
     * Get the inner filter
     * @return IRowFilter
     */
    public function getInnerFilter() {
        return $this->innerFilter;
    }
    
    /**
     * Set the inner filter
     * @param IRowFilter $filter
     * @return \Examples\HiveTransformETL\Component\Filter\InvertingFilterWrapper
     */
    public function setInnerFilter(IRowFilter $filter) {
        $this->innerFilter = $filter;
        return $this;
    }
    
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Component\Filter\IRowFilter::filter()
     */
    public function filter($row) {
        return !$this->getInnerFilter()->filter($row);
    }
    
    /**
     * Get an instance
     * @return \Examples\HiveTransformETL\Component\Filter\InvertingFilterWrapper
     */
    public static function instance() {
        return new static;
    }
}