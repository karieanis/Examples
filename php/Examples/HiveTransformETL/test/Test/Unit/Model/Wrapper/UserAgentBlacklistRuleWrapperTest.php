<?php
namespace Examples\HiveTransformETL\Test\Unit\Model\Wrapper;

use \Examples\HiveTransformETL\Model\Wrapper\UserAgentBlacklistRuleWrapper,
    \Examples\HiveTransformETL\Model\UserAgentRuleRow,
    \Examples\HiveTransformETL\Schema\SchemaFactory;

class UserAgentBlacklistRuleWrapperTest extends \PHPUnit_Framework_TestCase {
    public function testStandardFunctionality() {
        $schema = SchemaFactory::factory("\Examples\HiveTransformETL\Schema\Row\UserAgentBlacklistRule");
        $row = UserAgentRuleRow::create(
                array(
                    "yandex",
                    "1",
                    "yandex browser, yandex+browser, YandexMail",
                    "0",
                    "0",
                    "0"
                ), 
                $schema
        );
        
        $wrapper = UserAgentBlacklistRuleWrapper::fly($row);
        
        $this->assertEquals(array("yandex browser", "yandex+browser", "YandexMail"), $wrapper->getExceptions());
        $this->assertEquals("yandex", $wrapper->getUserAgent());
        $this->assertTrue($wrapper->isActive());
        $this->assertFalse($wrapper->matchUserAgentFromStart());
        $this->assertEquals(0, $wrapper->getPassCode());
        $this->assertEquals(0, $wrapper->getImpactCode());
        $this->assertEmpty($wrapper->getInactiveDate());
    }
}

return __NAMESPACE__;