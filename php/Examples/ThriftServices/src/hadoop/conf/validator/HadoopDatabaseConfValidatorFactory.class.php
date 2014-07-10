<?php
namespace Examples\ThriftServices\Hadoop\Conf\Validator;

/**
 * @final
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
final class HadoopDatabaseConfValidatorFactory {
    /**
     * Protected constructor, prevents direct instantiation
     * @codeCoverageIgnore
     */
    protected function __construct() {
        
    }
    
    /**
     * Generate validator instances used to validate passed configs base on the object signature
     * @param \Examples\ThriftServices\Hadoop\Conf\HadoopDatabaseConf $conf
     * @return \Examples\ThriftServices\Hadoop\Conf\Validator\IConfValidator
     */
    public static function factory(\Examples\ThriftServices\Hadoop\Conf\HadoopDatabaseConf $conf) {
        switch(get_class($conf)) {
            case "Examples\ThriftServices\Hive\Conf\HiveServer2Conf":
                $validator = new \Examples\ThriftServices\Hive\Conf\Validator\HiveServer2ConfValidator();
            break;
            
            default:
                $validator = new \Examples\ThriftServices\Hadoop\Conf\Validator\NullValidator();
            break;
        }
        
        return $validator;
    }
}