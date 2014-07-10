<?php
namespace Examples\ThriftServices\Hive\Conf\Validator;

/**
 * Validator for HiveServer2Conf objects
 * @final
 * @author Jeremy Rayner <jeremy@davros.com.au>
 */
final class HiveServer2ConfValidator implements \Examples\ThriftServices\Hadoop\Conf\Validator\IConfValidator {
    /**
     * @staticvar string
     */
    private static $ConfClass = "\Examples\ThriftServices\Hive\Conf\HiveServer2Conf"; 

    /**
     * Keys we expect to find in the conf object to be validated
     * @var array
     */
    private $required = array(
        'auth_mechanism'
    );
    
    /**
     * Potential values ew expect to find in the conf object (if values are constrained)
     * @var array
     */
    private $values = array(
        'auth_mechanism' => array(
            \Examples\ThriftServices\Auth\SASL\Mechanism\Anonymous::NOSASL,
            \Examples\ThriftServices\Auth\SASL\Mechanism\Anonymous::ANONYMOUS,
            \Examples\ThriftServices\Auth\SASL\Mechanism\Anonymous::PLAIN
        )
    );
    
    /**
     * Current errors
     * @var array
     */
    private $errors = array();
    
    /* 
     * @see \Examples\ThriftServices\Hadoop\Conf\Validator\IConfValidator::validate()
     */
    public function validate(\Examples\ThriftServices\Hadoop\Conf\HadoopDatabaseConf $conf) {
        if($this->validateInstanceType($conf)) {
            foreach($this->required as $key) {                
                if(!$this->validateKeyExists($conf, $key)) {
                    array_push($this->errors, sprintf(
                            "%s is a required key and is not present in %s",
                            $key, get_class($conf)
                        )
                    );
                    
                    continue;
                }
                
                if(!$this->validateValueIsAllowed($key, $conf[$key])) {
                    array_push($this->errors, sprintf(
                            "%s is not a valid value for %s! valid values are: %s",
                            $conf[$key], $key, $this->getAllowedValuesAsString($key)
                        )
                    );
                    continue;
                }
            }
        } else {
            array_push($this->errors, sprintf(
                    "Unexpected conf class %s passed, expected type %s",
                    get_class($conf), self::$ConfClass
                )
            );
        }
        
        return !$this->hasErrors();
    }
    
    /* 
     * @see \Examples\ThriftServices\Hadoop\Conf\Validator\IConfValidator::hasErrors()
     */
    public function hasErrors() {
        return count($this->errors) > 0;
    }
    
    /* 
     * @see \Examples\ThriftServices\Hadoop\Conf\Validator\IConfValidator::getErrors()
     */
    public function getErrors() {
        return $this->errors;
    }
    
    /**
     * Validate that the passed instance is a, or is extended from $confClass
     * @param \Examples\ThriftServices\Hadoop\Conf\HadoopDatabaseConf $conf
     * @return boolean
     */
    protected function validateInstanceType(\Examples\ThriftServices\Hadoop\Conf\HadoopDatabaseConf $conf) {
        $reflector = new \ReflectionClass($conf);
        return is_a($conf, self::$ConfClass) || $reflector->isSubclassOf(self::$ConfClass);
    }
    
    /**
     * Check if the passed key exists in the conf object
     * @param \Examples\ThriftServices\Hive\Conf\HiveServer2Conf $conf
     * @param mixed $key
     */
    protected function validateKeyExists(\Examples\ThriftServices\Hive\Conf\HiveServer2Conf $conf, $key) {
        return isset($conf[$key]);
    }
    
    /**
     * Check if there are constraints for the passed value, and if there is, validate that the value falls
     * within those constraints.
     * @param mixed $key
     * @param mixed $value
     * @return boolean
     */
    protected function validateValueIsAllowed($key, $value) {
        $isValid = true;
        
        if(isset($this->values[$key])) {
            $validValue = $this->values[$key];
            $isValid = is_array($validValue) ? in_array($value, $validValue) : $value == $validValue;
        }
        
        return $isValid;
    }
    
    /**
     * Retrieves potential values and casts them as a string (used when generating error messages)
     * @param mixed $key
     * @return string
     */
    protected function getAllowedValuesAsString($key) {
        $validValue = $this->values[$key];
        return is_array($validValue) ? implode(", ", $validValue) : $validValue;
    }
}