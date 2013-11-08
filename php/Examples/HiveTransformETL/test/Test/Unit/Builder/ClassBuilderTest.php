<?php
namespace Examples\HiveTransformETL\Test\Unit\Builder;

class ClassBuilderTest extends \Examples\HiveTransformETL\Test\Util\EMHiveTransformerApplicationTestCase {
    /**
     * @expectedException \Exception
     */
    public function testBuildWithInvalidSetterThrowsException() {
        $config = array(
            "class" => "\Examples\HiveTransformETL\Component\Runner",
            "properties" => 
                array(
                    array(
                        "class" => "\Examples\HiveTransformETL\Component\Reader",
                        "arguments" => array( "php://stdin" ),
                        "setter" => "setInFake"
                    )
                )
        );
        
        /* @var $obj \Examples\HiveTransformETL\Component\Runner */
        $builder = new \Examples\HiveTransformETL\Builder\ClassBuilder();
        $builder->build($config['class'], $config);
    }
    
    /**
     * @expectedException \ReflectionException
     */
    public function testBuildWithInvalidClassArgumentsThrowsException() {
        $config = array(
            "class" => "\Examples\HiveTransformETL\Component\Runner",
            "properties" => 
                array(
                    array(
                        "class" => "\Examples\HiveTransformETL\Component\FakeReader",
                        "arguments" => null,
                        "setter" => "setInFake"
                    )
                )
        );
        
        /* @var $obj \Examples\HiveTransformETL\Component\Runner */
        $builder = new \Examples\HiveTransformETL\Builder\ClassBuilder();
        $builder->build($config['class'], $config);
    }
}

return __NAMESPACE__;