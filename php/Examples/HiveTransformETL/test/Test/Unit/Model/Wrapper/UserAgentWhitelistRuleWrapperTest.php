<?php
namespace Examples\HiveTransformETL\Test\Unit\Model\Wrapper;

use \Examples\HiveTransformETL\Model\Wrapper\UserAgentWhitelistRuleWrapper,
    \Examples\HiveTransformETL\Model\UserAgentRuleRow,
    \Examples\HiveTransformETL\Schema\SchemaFactory;

class UserAgentWhitelistRuleWrapperTest extends \PHPUnit_Framework_TestCase {
    public function testStandardFunctionality() {
        $schema = SchemaFactory::factory("\Examples\HiveTransformETL\Schema\Row\UserAgentWhitelistRule");
        $row = UserAgentRuleRow::create(array("Mozilla/", "1", "1"), $schema);
        $wrapper = UserAgentWhitelistRuleWrapper::fly($row);
        
        $this->assertEquals("Mozilla/", $wrapper->getUserAgent());
        $this->assertTrue($wrapper->isActive());
        $this->assertTrue($wrapper->matchUserAgentFromStart());
        $this->assertEmpty($wrapper->getInactiveDate());
    }
}

return __NAMESPACE__;