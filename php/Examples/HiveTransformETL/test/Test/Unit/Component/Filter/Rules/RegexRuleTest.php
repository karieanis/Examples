<?php
namespace Examples\HiveTransformETL\Test\Unit\Component\Filter\Rules;

use Examples\HiveTransformETL\Component\Filter\Rules\RegexRule,
    Examples\HiveTransformETL\Model\IpExclusionRule;

class RegexRuleTest extends \PHPUnit_Framework_TestCase {
    public function testApply() {
        $model = IpExclusionRule::instance();
        $model->setRegex("^10\.");
        
        $rule = RegexRule::wrap($model);
        $this->assertTrue($rule->apply("10.0.0.1"));
        $this->assertFalse($rule->apply("192.168.0.1"));
    }
}

return __NAMESPACE__;