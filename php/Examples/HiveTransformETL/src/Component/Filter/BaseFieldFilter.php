<?php
namespace Examples\HiveTransformETL\Component\Filter;

/**
 * Basic filter logic for field based values
 * 
 * @abstract
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
abstract class BaseFieldFilter {
    /**
     * @var string
     */
    protected $fieldName;
    
    /**
     * Get the field name
     * @return string
     */
    public function getFieldName() {
        return $this->fieldName;
    }
    
    /**
     * Set the field name
     * @param string $fieldName
     * @return \Examples\HiveTransformETL\Component\Filter\BaseFieldFilter
     */
    public function setFieldName($fieldName) {
        $this->fieldName = $fieldName;
        return $this;
    }
}