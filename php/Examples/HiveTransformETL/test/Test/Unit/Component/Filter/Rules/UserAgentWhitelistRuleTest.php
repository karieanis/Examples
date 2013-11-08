<?php
namespace Examples\HiveTransformETL\Test\Unit\Component\Filter\Rules;

use \Examples\HiveTransformETL\Component\Filter\Rules\UserAgentWhitelistRule,
    \Examples\HiveTransformETL\Model\UserAgentRuleRow,
    \Examples\HiveTransformETL\Schema\SchemaFactory;;

class UserAgentWhitelistRuleTest extends \PHPUnit_Framework_TestCase {
    protected $schema;
    
    protected function setUp() {
        $this->schema = SchemaFactory::factory("\Examples\HiveTransformETL\Schema\Row\UserAgentWhitelistRule");
    }
    
    protected function tearDown() {
        unset($this->schema);
    }
    
    public function testApplyWithMatchFromStartRuleReturnsFalse() {
        $agent = "Fake Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)";
        $row = UserAgentRuleRow::create( 
            array(
                $this->schema->getIndexFor("user_agent") => "Mozilla/",
                $this->schema->getIndexFor("match_from_start") => 1,
                $this->schema->getIndexFor("active") => 1
            ),
            $this->schema
        );
        
        $rule = UserAgentWhitelistRule::wrap($row);
        $this->assertFalse($rule->apply($agent));
    }
    
    public function testApplyWithNoMatchFromStartRuleReturnsTrue() {
        $agent = "Fake Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)";
        $row = UserAgentRuleRow::create( 
            array(
                    $this->schema->getIndexFor("user_agent") => "Mozilla/",
                    $this->schema->getIndexFor("match_from_start") => 0,
                    $this->schema->getIndexFor("active") => 1
            ),
            $this->schema
        );
    
        $rule = UserAgentWhitelistRule::wrap($row);
        $this->assertTrue($rule->apply($agent));
    }
    
    public function testIsDeprecatedReturnsFalseWithActiveModel() {
        $row = UserAgentRuleRow::create( 
            array(
                    $this->schema->getIndexFor("user_agent") => "Mozilla/",
                    $this->schema->getIndexFor("match_from_start") => 1,
                    $this->schema->getIndexFor("active") => 1
            ),
            $this->schema
        );
        
        $rule = UserAgentWhitelistRule::wrap($row);
        $this->assertFalse($rule->isDeprecated());
    }
}

return __NAMESPACE__;