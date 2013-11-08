<?php
namespace Examples\HiveTransformETL\Component\Filter\Rules;

/**
 * Basic rule business logic layer class
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
abstract class BaseRule {
    /**
     * @var mixed
     */
    protected $ruleModel;
    
    /**
     * Get the rule data model
     * @return mixed
     */
    public function getRuleModel() {
        return $this->ruleModel;
    }
    
    /**
     * Set the rule data model
     * @param mixed $model
     * @return \Examples\HiveTransformETL\Component\Filter\Rules\BaseRule
     */
    public function setRuleModel($model) {
        $this->ruleModel= $model;
        return $this;
    }
    
    /**
     * Wrap the rule data model in a concrete implementation of this class
     * @static
     * @param mixed $model
     * @return \Examples\HiveTransformETL\Component\Filter\Rules\BaseRule
     */
    public static function wrap($model) {
        $instance = new static;
        $instance->setRuleModel($model);
        
        return $instance;
    }
}