<?php
namespace Examples\HiveTransformETL\Model\Wrapper;

/**
 * Basic user agent rule wrapper class
 * @abstract
 * @author Jeremy Rayner <jeremy@davros.com.au>
 * @codeCoverageIgnore
 */
abstract class AbstractUserAgentRuleWrapper {
    /**
     * 
     * @var \Examples\HiveTransformETL\Model\UserAgentRuleRow
     */
    protected $innerRow;
    
    /**
     * Constructor
     * @final
     * @param \Examples\HiveTransformETL\Model\UserAgentRuleRow $row
     */
    final public function __construct(\Examples\HiveTransformETL\Model\UserAgentRuleRow $row) {
        $this->setInnerRow($row)
            ->init();
    }
    
    /**
     * Implement in child classes for specific logic upon construction 
     */
    protected function init() {

    }
    
    /**
     * Wrap the passed rule row an a concrete implementation of this class
     * @final
     * @param \Examples\HiveTransformETL\Model\UserAgentRuleRow $row
     * @return \Examples\HiveTransformETL\Model\Wrapper\AbstractUserAgentRuleWrapper
     */
    final public static function wrap(\Examples\HiveTransformETL\Model\UserAgentRuleRow $row) {
        return new static($row);
    }
    
    /**
     * Get the internal rule row
     * @return \Examples\HiveTransformETL\Model\UserAgentRuleRow
     */
    protected function getInnerRow() {
        return $this->innerRow;
    }
    
    /**
     * Set the internal rule row
     * @param \Examples\HiveTransformETL\Model\UserAgentRuleRow $row
     * @return \Examples\HiveTransformETL\Model\Wrapper\AbstractUserAgentRuleWrapper
     */
    protected function setInnerRow(\Examples\HiveTransformETL\Model\UserAgentRuleRow $row) {
        $this->innerRow = $row;
        return $this;
    }
}