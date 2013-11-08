<?php
namespace Examples\HiveTransformETL\Component\Filter\Rules;

use Examples\HiveTransformETL\Application\Conf\HiveTransformerConfVars;

/**
 * Container for configuration logic relevant to rules. Application conf is wrapped to enforce immutability.
 * 
 * @final
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
final class RuleConf {
    /**
     * @var \Examples\HiveTransformETL\Conf\ImmutableConf
     */
    protected $innerConf;
    
    /**
     * Cosntructor
     * @param \Examples\HiveTransformETL\Conf\ImmutableConf $conf
     */
    protected function __construct(\Examples\HiveTransformETL\Conf\ImmutableConf $conf) {
        $this->innerConf = $conf;
    }
    
    /**
     * Returns the location of the ip filtration rules
     * @return string
     */
    public function getIpRulesDir() {
        return $this->innerConf[HiveTransformerConfVars::IP_FILTER_RULES_DIR];
    }
    
    /**
     * Returns the location of the user agent inclusion / exclusion rules
     * @return string
     */
    public function getUserAgentRulesDir() {
        return $this->innerConf[HiveTransformerConfVars::USERAGENT_FILTER_RULES_DIR];
    }
    
    /**
     * Wraps teh passed configuration object in an immutable conf wrapper, then returns an instance of RuleConf
     * 
     * @param \Examples\HiveTransformETL\Conf\BaseConf $conf
     * @return \Examples\HiveTransformETL\Component\Filter\Rules\RuleConf
     */
    public static function wrap(\Examples\HiveTransformETL\Conf\BaseConf $conf) {
        return new static(
                \Examples\HiveTransformETL\Conf\ImmutableConf::wrap($conf)
        );
    }
}