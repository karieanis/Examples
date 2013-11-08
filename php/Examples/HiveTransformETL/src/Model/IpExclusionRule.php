<?php
namespace Examples\HiveTransformETL\Model;

/**
 * Ip exclusion rule data model
 * @final
 * @author Jeremy Rayner <jeremy@davros.com.au>
 * @codeCoverageIgnore
 */
final class IpExclusionRule {
    /**
     * @var string
     */
    protected $regex;
    
    /**
     * Get an instance
     * @static
     * @return \Examples\HiveTransformETL\Model\IpExclusionRule
     */
    public static function instance() {
        return new static;
    }
    
    /**
     * Get the regular expression
     * @return string
     */
    public function getRegex() {
        return $this->regex;
    }
    
    /**
     * Set the regular expresion
     * @param string $regex
     * @return \Examples\HiveTransformETL\Model\IpExclusionRule
     */
    public function setRegex($regex) {
        $this->regex = $regex;
        return $this;
    }
}