<?php
namespace Examples\HiveTransformETL\Translator;

/**
 * This interface describes the methods required of a service container for i/o translation
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
interface ITranslatorContainer {
    /**
     * Translate the incoming row object
     * @param mixed $row
     * @return mixed
     */
    public function translateInput($row);
    /**
     * Translate the outgoing row object
     * @param mixed $row
     * @return mixed
     */
    public function translateOutput($row);
}