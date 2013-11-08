<?php
namespace Examples\HiveTransformETL\Application;

use \Examples\HiveTransformETL\Conf\IConfigurable;

/**
 * Application context object
 * @final
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
final class ApplicationContext implements IConfigurable {
    /**
     * 
     * @var \Examples\HiveTransformETL\Application\Context
     */
    protected static $instance;

    /**
     * 
     * @var \Examples\HiveTransformETL\Schema\ISchemaMap
     */
    protected static $inSchema;
    
    /**
     * 
     * @var \Examples\HiveTransformETL\Schema\ISchemaMap
     */
    protected static $outSchema;
    
    /**
     * 
     * @var \Examples\HiveTransformETL\Application\Conf\HiveTransformerConf
     */
    protected static $conf;
    
    /**
     * @codeCoverageIgnore
     */
    protected function __construct() {}
    
    /**
     * Get an instance
     * @return \Examples\HiveTransformETL\Application\ApplicationContext
     */
    public static function getInstance() {
        if(!(static::$instance instanceof static)) {
            static::$instance = new static();
        }
        
        return static::$instance;
    }

    /**
     * Get the input schema
     * @return \Examples\HiveTransformETL\Schema\ISchemaMap
     */
    public function getInSchema() {
        return static::$inSchema;
    }
    
    /**
     * Set the input schema
     * @param \Examples\HiveTransformETL\Schema\ISchemaMap $map
     * @return \Examples\HiveTransformETL\Application\ApplicationContext
     */
    public function setInSchema(\Examples\HiveTransformETL\Schema\ISchemaMap $map) {
        static::$inSchema = $map;
        return $this;
    }
    
    /**
     * Get the output schema
     * @return \Examples\HiveTransformETL\Schema\ISchemaMap
     */
    public function getOutSchema() {
        return static::$outSchema;
    }
    
    /**
     * Set the output schema
     * @param \Examples\HiveTransformETL\Schema\ISchemaMap $map
     * @return \Examples\HiveTransformETL\Application\ApplicationContext
     */
    public function setOutSchema(\Examples\HiveTransformETL\Schema\ISchemaMap $map) {
        static::$outSchema = $map;
        return $this;
    }
    
    /**
     * Set the application configuration
     * @param \Examples\HiveTransformETL\Conf\BaseConf $conf
     * @return \Examples\HiveTransformETL\Application\ApplicationContext
     */
    public function setConf(\Examples\HiveTransformETL\Conf\BaseConf $conf) {
        static::$conf = $conf;
        return $this;
    }
    
    /**
     * Get the application configuration
     * @return \Examples\HiveTransformETL\Application\Conf\HiveTransformerConf
     */
    public function getConf() {
        return static::$conf;
    }
}