<?php
namespace Examples\HiveTransformETL\Translator;

/**
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
final class NullTranslator implements ITranslatorContainer {
    /**
     * @var \Examples\HiveTransformETL\Translator\NullTranslator
     */
    protected static $instance;
    
    /**
     * Constrcutor
     */
    protected function __construct() {}
    
    /**
     * @return \Examples\HiveTransformETL\Translator\NullTranslator
     */
    public static function getInstance() {
        if(!(self::$instance instanceof self)) {
            self::$instance = new self;
        }
        
        return self::$instance;
    }
    
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Translator\ITranslatorContainer::translateInput()
     */
    public function translateInput($row) {
        return $row;
    }
    
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Translator\ITranslatorContainer::translateOutput()
     */
    public function translateOutput($row) {
        return $row;
    }
}