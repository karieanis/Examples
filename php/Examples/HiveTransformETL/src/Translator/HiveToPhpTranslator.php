<?php
namespace Examples\HiveTransformETL\Translator;

/**
 * Responsible for translating hive representations of certain primitives for php
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
class HiveToPhpTranslator implements IHiveTranslator {
    /**
     * The translation dictionary
     * @var array
     */
    protected $dictionary = array(
        "\N" => NULL                // nulls are represented in Hive output as \N
    );
    
    /**
     * Get an instance
     * @codeCoverageIgnore
     * @return \Examples\HiveTransformETL\Translator\HiveToPhpTranslator
     */
    public static function instance() {
        return new static;
    }
    
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Translator\IHiveTranslator::translate()
     */
    public function translate(\Examples\HiveTransformETL\Model\HiveRow $row) {
        $dictionary = $this->getDictionary();
        $translatable = array_keys($dictionary);
        
        // for each item in the row
        foreach($row as $key => $value) {
            if(in_array($value, $translatable, true)) { // see if there is a translation
                $row[$key] = $dictionary[$value]; // translate if there is
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