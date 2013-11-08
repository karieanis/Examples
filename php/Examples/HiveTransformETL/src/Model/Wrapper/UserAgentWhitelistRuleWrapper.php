<?php
namespace Examples\HiveTransformETL\Model\Wrapper;

/**
 * Wrapper class used to access relevant information from a whitelist rule without implict knowledge of the schema
 * @final
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
final class UserAgentWhitelistRuleWrapper extends AbstractUserAgentRuleWrapper {
    /**
     * A shared flyweight object
     * @var \Examples\HiveTransformETL\Model\Wrapper\UserAgentWhitelistRuleWrapper
     */
    protected static $flyweight;
    
    /**
     * Get the user agent
     * @return string
     */
    public function getUserAgent() {
        $row = $this->getInnerRow();
        return (string)$row['user_agent'];
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
     * Return true of the record requires agent matching from the start of the agent string
     * @return boolean
     */
    public function matchUserAgentFromStart() {
        $row = $this->getInnerRow();
        return (int)$row['match_from_start'] === 1;
    }
    
    /**
     * Utilise the flyweight object to wrap the passed blacklist object. This cuts down on the number of construct
     * instructions required by an application
     * 
     * @param \Examples\HiveTransformETL\Model\UserAgentRuleRow $row
     * @return \Examples\HiveTransformETL\Model\Wrapper\UserAgentWhitelistRuleWrapper
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