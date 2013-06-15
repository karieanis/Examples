package com.examples.utils.validator {
    /**
     * Basic validator interface
     * @author Jeremy Rayner <jeremy@davros.com.au>
     * 
     */
    public interface IValidator {
        
        /**
         * Validate the passed validatable, return true or false depending on the business logic implementation which dictates the
         * validity of validatable.
         * 
         * @param validatable
         * @return Boolean
         * 
         */
        function validate(validatable:*):Boolean;
    }
}