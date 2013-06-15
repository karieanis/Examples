package com.examples.utils.validator {
    import com.examples.utils.Configuration;
    
    /**
     * Validator used to validate the contents of a configuration.  
     * @author Jeremy Rayner <jeremy@davros.com.au>
     * 
     */
    public final class ConfigurationValidator implements IValidator {
        private var _invalid:Array = [];
        
        /**
         * Constructor 
         * 
         */
        public function ConfigurationValidator() {
            
        }
        
        /**
         * Retrieve validation failure information. Will be empty if nothing has been validated, or the last object to
         * be validated was valid.
         * @return Array
         * 
         */
        public function get invalid():Array {
            return _invalid;
        }
        
        /**
         * Validate that the passed configuration object contains the base information required for the tracker to function
         * @param validatable   A configuration object
         * @return boolean
         * 
         */
        public function validate(validatable:*):Boolean {
            reset();
            
            validatable = validatable as Configuration;
            var _required:Array = validatable.getValue("configuration.required.keys").split(",") as Array,
                isValid:Boolean = true, 
                pos:Number, key:String;
            
            for(pos = 0; (key = _required[pos]); pos++) {
                if(null === validatable.getValue(key)) {
                    isValid = false;
                    _invalid.push(key);
                }
            }
            
            return isValid;
        }
        
        /**
         * Reset the holder for validation failures
         * 
         */
        protected function reset():void {
            _invalid = [];
        }
    }
}