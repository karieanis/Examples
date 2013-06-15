<?php
namespace Examples\Hive;

/**
 * An exception class for HivePDO errors
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
class HivePDOException extends \Exception {
    /**
     * Standard format string for error data
     * @var string
     */
    protected static $messageFormat = "SQLSTATE[%1\$s]: %2\$s";
    
    /**
     * Manufacture a HivePDOException using the passed error info array
     * @param array $errorInfo
     * @return \Examples\Hive\HivePDOException
     * @codeCoverageIgnore
     */
    public static function factory(array $errorInfo) {
        return new static(sprintf(static::$messageFormat, $errorInfo[0], $errorInfo[2]), $errorInfo[1]);
    }
}