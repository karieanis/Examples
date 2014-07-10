<?php
namespace Examples\ThriftServices\Hadoop\Conf\Validator;

/**
 * Base configuration validator interface
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
interface IConfValidator {
    /**
     * Validate the passed conf object
     * @param \Examples\ThriftServices\Hadoop\Conf\HadoopDatabaseConf $conf
     * @return boolean
     */
    public function validate(\Examples\ThriftServices\Hadoop\Conf\HadoopDatabaseConf $conf);
    /**
     * Has this validator detected any errors?
     * @return boolean
     */
    public function hasErrors();
    /**
     * Retrieve any current errors
     * @return array
     */
    public function getErrors();
}