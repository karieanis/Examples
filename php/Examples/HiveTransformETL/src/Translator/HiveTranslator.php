<?php
namespace Examples\HiveTransformETL\Translator;

/**
 * Container object for in / out translator logic
 * @author Jeremy Rayner <jeremy@davros.com.au>
 * @codeCoverageIgnore
 */
final class HiveTranslator {
    /**
     * @staticvar HiveTranslator
     */
    protected static $instance;
    
    /**
     * @var HiveToPhpTranslator
     */
    protected $inTranslator;
    /**
     * @var PhpToHiveTranslator
     */
    protected $outTranslator;
    
    /**
     * Constructor
     */
    protected function __construct() {
        $this->inTranslator = HiveToPhpTranslator::instance();
        $this->outTranslator = PhpToHiveTranslator::instance();
    }
    
    /**
     * Get the singleton
     * @return \Examples\HiveTransformETL\Translator\HiveTranslator
     */
    public static function getInstance() {
        if(!(self::$instance instanceof self)) {
            self::$instance = new self;
        }
        
        return self::$instance;
    }
    
    /**
     * Translate incoming input using the inTranslator
     * 
     * @param \Examples\HiveTransformETL\Model\HiveRow $row
     * @return \Examples\HiveTransformETL\Model\HiveRow
     */
    public function translateInput($row) {
        return $this->inTranslator->translate($row);
    }
    
    /**
     * Translate the outgoing input using the outTranslator
     * 
     * @param \Examples\HiveTransformETL\Model\HiveRow $row
     * @return \Examples\HiveTransformETL\Model\HiveRow
     */
    public function translateOutput($row) {
        return $this->outTranslator->translate($row);
    }
}