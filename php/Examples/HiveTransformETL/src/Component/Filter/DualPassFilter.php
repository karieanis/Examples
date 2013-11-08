<?php
namespace Examples\HiveTransformETL\Component\Filter;

/**
 * Dual pass filtration class. Will return true if either of the two inner filters return true.
 * 
 * @final
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
final class DualPassFilter implements IRowFilter {
    /**
     * @var IRowFilter
     */
    protected $leftFilter;
    /**
     * @var IRowFilter
     */
    protected $rightFilter;
    
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Component\Filter\IRowFilter::filter()
     */
    public function filter($row) {
        return $this->getLeftFilter()->filter($row) || $this->getRightFilter()->filter($row);
    }
    
    /**
     * Get the left filter
     * @return IRowFilter
     */
    public function getLeftFilter() {
        return $this->leftFilter;
    }
    
    /**
     * Get the right filter
     * @return IRowFilter
     */
    public function getRightFilter() {
        return $this->rightFilter;
    }
    
    /**
     * Set the left filter
     * @param IRowFilter $filter
     * @return \Examples\HiveTransformETL\Component\Filter\DualPassFilter
     */
    public function setLeftFilter(IRowFilter $filter) {
        $this->leftFilter = $filter;
        return $this;
    }
    
    /**
     * Set right filter
     * @param IRowFilter $filter
     * @return \Examples\HiveTransformETL\Component\Filter\DualPassFilter
     */
    public function setRightFilter(IRowFilter $filter) {
        $this->rightFilter = $filter;
        return $this;
    }

    /**
     * Get an instance
     * @return \Examples\HiveTransformETL\Component\Filter\DualPassFilter
     */
    public static function instance() {
        return new static;
    }
}