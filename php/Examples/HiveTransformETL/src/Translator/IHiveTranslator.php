<?php
namespace Examples\HiveTransformETL\Translator;

/**
 * Interface describing the methods required of Hive translation implementations
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
interface IHiveTranslator {
    /**
     * Translate a row
     * @param \Examples\HiveTransformETL\Model\HiveRow $row
     */
    public function translate(\Examples\HiveTransformETL\Model\HiveRow $row);
    /**
     * Get the translation dictionary
     * @return array
     */
    public function getDictionary();
    /**
     * Set the translation dictionary
     * @param array $dictionary
     * @return IHiveTranslator
     */
    public function setDictionary($dictionary);
}