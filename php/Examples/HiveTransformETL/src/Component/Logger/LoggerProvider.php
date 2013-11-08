<?php
namespace Examples\HiveTransformETL\Component\Logger;

use \Examples\HiveTransformETL\Util\ReflectionUtils;

/**
 * Logging provider - returns varying loggers depending on the class signature of the passed object
 * @final
 * @codeCoverageIgnore
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
final class LoggerProvider {
    /**
     * Get a logger for the passed object
     * @static
     * @param string $forClass
     * @return \Logger
     */
    public static function getLogger($forClass) {
        $logger = null;
        $name = ReflectionUtils::resolveClassName($forClass);
        
        switch($name) {
            case "Examples\HiveTransformETL\Builder\ClassBuilder":
                $logger = \Logger::getLogger('emBuilderLogger');
            break;
            
            default:
                $logger = \Logger::getLogger('emFileLogger');
            break;
        }
        
        return $logger;
    }
    
    /**
     * Get the error logger
     * @static
     * @return \Logger
     */
    public static function getErrorLogger() {
        return \Logger::getLogger('emErrorLogger');
    }
}