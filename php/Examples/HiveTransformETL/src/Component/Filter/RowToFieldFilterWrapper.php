<?php
namespace Examples\HiveTransformETL\Component\Filter;

/**
 * Adapter class for field based filtration upon a row object. Extract the field content from the row then passes it to the
 * inner filter.
 * 
 * @final
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
final class RowToFieldFilterWrapper extends BaseFieldFilter implements IRowFilter {
    /**
     * @var IFieldValueFilter
     */
    protected $innerFilter;
    
    /**
     * Get the inner filter
     * @return IFieldValueFilter
     */
    public function getInnerFilter() {
        return $this->innerFilter;
    }
    
    /**
     * Set the inner filter
     * @param IFieldValueFilter $filter
     * @return \Examples\HiveTransformETL\Component\Filter\RowToFieldFilterWrapper
     */
    public function setInnerFilter(IFieldValueFilter $filter) {
        $this->innerFilter = $filter;
        return $this;
    }
    
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Component\Filter\IRowFilter::filter()
     */
    public function filter($row) {
        return $this->getInnerFilter()->filter($row[$this->getFieldName()]);
    }
    
    /**
     * Get an instance
     * @return \Examples\HiveTransformETL\Component\Filter\RowToFieldFilterWrapper
     */
    public static function instance() {
        return new static;
    }
}