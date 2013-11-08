<?php
namespace Examples\HiveTransformETL\Test\Unit\Component\Filter\Rules;

use \Examples\HiveTransformETL\Component\Filter\Rules\UserAgentBlacklistRule,
    \Examples\HiveTransformETL\Model\UserAgentRuleRow,
    \Examples\HiveTransformETL\Schema\SchemaFactory;

class UserAgentBlacklistRuleTest extends \PHPUnit_Framework_TestCase {
    protected $schema;
    
    protected function setUp() {
        $this->schema = SchemaFactory::factory("\Examples\HiveTransformETL\Schema\Row\UserAgentBlacklistRule");
    }
    
    protected function tearDown() {
        unset($this->schema);
    }
    
    public function testApplyWithMatchFromStartAndNoExceptionsReturnsTrue() {
        $agent = "shopwiki";
        $row = UserAgentRuleRow::create(
                array(
                    $this->schema->getIndexFor('user_agent') => "shopwiki",
                    $this->schema->getIndexFor('active') => 1,
                    $this->schema->getIndexFor('exceptions') => "",
                    $this->schema->getIndexFor('match_from_start') => 1
                ),
                $this->schema
        );
        
        $rule = UserAgentBlacklistRule::wrap($row);
        $this->assertTrue($rule->apply($agent));
    }
    
    public function testApplyWithMatchFromStartAndApplicableExceptionReturnsFalse() {
        $agent = "shopwiki";
        $row = UserAgentRuleRow::create(
                array(
                        $this->schema->getIndexFor('user_agent') => "shopwiki",
                        $this->schema->getIndexFor('active') => 1,
                        $this->schema->getIndexFor('exceptions') => "wiki",
                        $this->schema->getIndexFor('match_from_start') => 1
                ),
                $this->schema
        );
    
        $rule = UserAgentBlacklistRule::wrap($row);
        $this->assertFalse($rule->apply($agent));
    }
    
    public function testApplyWithNoMatchFromStartAndNoExceptionsReturnsTrue() {
        $agent = "fake shopwiki";
        $row = UserAgentRuleRow::create(
                array(
                        $this->schema->getIndexFor('user_agent') => "shopwiki",
                        $this->schema->getIndexFor('active') => 1,
                        $this->schema->getIndexFor('exceptions') => "",
                        $this->schema->getIndexFor('match_from_start') => 0
                ),
                $this->schema
        );
    
        $rule = UserAgentBlacklistRule::wrap($row);
        $this->assertTrue($rule->apply($agent));
    }
    
    public function testApplyWithMatchFromStartAndNoExceptionsReturnsFalse() {
        $agent = "fake shopwiki";
        $row = UserAgentRuleRow::create(
                array(
                        $this->schema->getIndexFor('user_agent') => "shopwiki",
                        $this->schema->getIndexFor('active') => 1,
                        $this->schema->getIndexFor('exceptions') => "",
                        $this->schema->getIndexFor('match_from_start') => 1
                ),
                $this->schema
        );
    
        $rule = UserAgentBlacklistRule::wrap($row);
        $this->assertFalse($rule->apply($agent));
    }

    public function testIsDeprecatedReturnsFalse() {
        $row = UserAgentRuleRow::create(
                array(
                        $this->schema->getIndexFor('user_agent') => "shopwiki",
                        $this->schema->getIndexFor('active') => 1,
                        $this->schema->getIndexFor('exceptions') => "",
                        $this->schema->getIndexFor('match_from_start') => 1
                ),
                $this->schema
        );
        
        $rule = UserAgentBlacklistRule::wrap($row);
        $this->assertFalse($rule->isDeprecated());
    }
}

return __NAMESPACE__;