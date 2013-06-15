package com.examples.utils {
    import flash.external.ExternalInterface;
    import com.examples.utils.BrowserProxy;
    
    /**
     * External Interface abstraction object. 
     * @author Jeremy Rayner <jeremy@davros.com.au>
     * 
     */
    public class ExternalInterface {
        /**
         * Constructor
         */
        public function ExternalInterface() {

        }
        
        /**
         * Check if javascript is currently available
         * @return Boolean
         * 
         */
        public function get available(): Boolean {
            return flash.external.ExternalInterface.available;
        }
        
        /**
         * Add a javascript callback
         * @param functionName
         * @param closure
         * 
         */
        public function addCallback(functionName:String, closure:Function):void {
            flash.external.ExternalInterface.addCallback(functionName, closure);
        }
        
        /**
         * Invoke a call to a javascript method
         * @param functionName
         * @param parameters
         * @return mixed
         * 
         */
        public function call(functionName:String, ... parameters):* {
            parameters.unshift(functionName);
            return flash.external.ExternalInterface.call.apply(null, parameters);
        }
    }
}