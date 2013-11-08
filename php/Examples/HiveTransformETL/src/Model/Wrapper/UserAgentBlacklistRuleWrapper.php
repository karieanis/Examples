<?php
namespace Examples\HiveTransformETL\Model\Wrapper;

/**
 * Wrapper class used to access relevant information from a blacklist rule without implicit knowledge of the schema
 * @final
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
final class UserAgentBlacklistRuleWrapper extends AbstractUserAgentRuleWrapper {
    /**
     * A shared flyweight object
     * @staticvar \Examples\HiveTransformETL\Model\Wrapper\UserAgentBlacklistRuleWrapper
     */
    protected static $flyweight;
    
    /**
     * 
     * @var array
     */
    protected $exceptions;
    
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Model\Wrapper\AbstractUserAgentRuleWrapper::init()
     */
    protected function init() {
        $row = $this->getInnerRow();
        $this->exceptions = array_filter(array_map("trim", explode(",", $row['exceptions']))); // parse exceptions
    }
    
    /**
     * Get the user agent
     * @return string
     */
    public function getUserAgent() {
        $row = $this->getInnerRow();
        return (string)$row['user_agent'];
    }
    
    /**
     * Get any exceptions
     * @return array
     */
    public function getExceptions() {
        return $this->exceptions;
    }
    
    /**
     * Get the pass code for the record
     * http://www.abc.org.uk/PageFiles/49/ABC%20Steps%20To%20Implementation_v5.pdf
     * 
     * 0 - this record is not need when using the dual pass approach
     * 1 - this record always always required
     * 
     * @return number
     */
    public function getPassCode() {
        $row = $this->getInnerRow();
        return (int)$row['pass_code'];
    }
    
    /**
     * Get the impact code for the record
     * http://www.abc.org.uk/PageFiles/49/ABC%20Steps%20To%20Implementation_v5.pdf
     *  
     * 0 - record impacts page impression measurement
     * 1 - record impacts ad impression measurement
     * 2 - record impacts boths
     * 
     * @return number
     */
    public function getImpactCode() {
        $row = $this->getInnerRow();
        return (int)$row['impact_code'];
    }
    
    /**
     * Get the inactive date
     * @return string
     */
    public function getInactiveDate() {
        $row = $this->getInnerRow();
        return (string)$row['inactive_date'];
    }
    
    /**
     * Is the record currently active?
     * @return boolean
     */
    public function isActive() {
        $row = $this->getInnerRow();
        return (int)$row['active'] === 1;
    }

    /**
     * Does the record have any exceptions?
     * @return boolean
     */
    public function hasExceptions() {
        return count($this->exceptions) > 0;
    }
    
    /**
     * Return true of the record requires agent matching from the start of the agent string
     * @return boolean
     */
    public function matchUserAgentFromStart() {
        $row = $this->getInnerRow();
        return (int)$row['match_from_start'] === 1;
    }

    /**
     * Utilise the flyweight object to wrap the passed blacklist object. This cuts down on the number of construct
     * instructions required for an application.
     * 
     * @static
     * @param \Examples\HiveTransformETL\Model\UserAgentRuleRow $row
     * @return \Examples\HiveTransformETL\Model\Wrapper\UserAgentBlacklistRuleWrapper
     */
    public static function fly(\Examples\HiveTransformETL\Model\UserAgentRuleRow $row) {
        if(is_null(self::$flyweight)) { // flyweight has not been initialised
            self::$flyweight = self::wrap($row);
        } else if($row !== self::$flyweight->getInnerRow()) { // if existing flyweight is not already wrapping this row
            self::$flyweight->setInnerRow($row)->init();
        }
    
        return self::$flyweight;
    }
}