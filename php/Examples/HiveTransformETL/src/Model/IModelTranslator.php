<?php
namespace Examples\HiveTransformETL\Model;

/**
 * Defines an interface for model translators. Primarily used to translate third party data models into application
 * specific data models.
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
interface IModelTranslator {
    /**
     * Translate a model into a specific format
     * @param mixed $model
     * @return mixed
     */
    public function translate($model);
}