<?php
namespace Examples\HiveTransformETL\Translator;

/**
 * Responsible for translation php representations of certain primitives for hive
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
class PhpToHiveTranslator implements IHiveTranslator {
    /**
     * @var array
     */
    protected $dictionary = array(
        "\N" => NULL                // nulls in php are represented in Hive as \N
    );
    
    /**
     * Get an instance
     * @codeCoverageIgnore
     * @return \Examples\HiveTransformETL\Translator\PhpToHiveTranslator
     */
    public static function instance() {
        return new static;
    }
    
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Translator\IHiveTranslator::translate()
     */
    public function translate(\Examples\HiveTransformETL\Model\HiveRow $row) {
        $dictionary = $this->getDictionary();
        
        // for each item in the row
        foreach($row as $key => $value) {
            // check if the value is in the dictionary
            if(false !== ($idx = array_search($row[$key], $dictionary, true))) {
                $row[$key] = $idx; // translate if it is
            }
        }
        
        return $row;
    }
    
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Translator\IHiveTranslator::getDictionary()
     */
    public function getDictionary() {
        return $this->dictionary;
    }
    
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Translator\IHiveTranslator::setDictionary()
     */
    public function setDictionary($dictionary) {
        // @codeCoverageIgnoreStart
        $this->dictionary = $dictionary;
        return $this;
        // @codeCoverageIgnoreEnd
    }
}