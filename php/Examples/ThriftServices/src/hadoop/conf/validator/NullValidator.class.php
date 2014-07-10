<?php
namespace Examples\ThriftServices\Hadoop\Conf\Validator;

/**
 * Null validator implementation. Ensures fluent interface for all conf objects which have no
 * specific validators.
 * 
 * @final
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
final class NullValidator implements IConfValidator {
    /* 
     * @see \Examples\ThriftServices\Hadoop\Conf\Validator\IConfValidator::validate()
     */
    public function validate(\Examples\ThriftServices\Hadoop\Conf\HadoopDatabaseConf $conf) {
        return true;
    }
    
    /* 
     * @see \Examples\ThriftServices\Hadoop\Conf\Validator\IConfValidator::hasErrors()
     */
    public function hasErrors() {
        return false;
    }
    
    /* 
     * @see \Examples\ThriftServices\Hadoop\Conf\Validator\IConfValidator::getErrors()
     */
    public function getErrors() {
        return array();
    }
}