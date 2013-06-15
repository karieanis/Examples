package com.examples.utils {
    import com.examples.utils.Configuration;
    import com.examples.utils.ExternalInterface;
    
    /**
     * Proxy object used to handle interaction between a flash application and the current browser
     * @author Jeremy Rayner <jeremy@davros.com.au>
     * 
     */
    public class BrowserProxy {
        private var _jsInterface:ExternalInterface; // wrapper around flash ExternalInterface, primarily for unit testing
        private var _config:Configuration; // configuration object
        
        /**
         * Constructor
         * 
         */
        public function BrowserProxy() {
            initialize();
        }

        /**
         * Invokes a javascript call using the current javascript interface implementation
         * @param functionName      The name of a function, or a valid js anonymous function
         * @param parameters        parameters to be passed to the function
         * @return mixed
         * 
         */
        public function call(functionName:String, ... parameters):* {
            var callableArgs:Array = parameters || [];
            callableArgs.unshift(functionName);
           
            return jsInterface.call.apply(null, callableArgs);
        }
        
        /**
         * Invokes a javascript call using the current javascript interface implementation. Will invoke using apply rather than
         * an ordinary call.
         * @param functionName      The name of a function, or a valid js anonymous function
         * @param scope             Defines what 'this' represents within the scope of the js function to be called
         * @param parameters        parameters to be passed to the function
         * @return mixed
         * 
         */
        public function apply(functionName:String, scope:String = null, ... parameters):Object {
            var callable:String = functionName + ".apply",
                callableArgs:Array = parameters as Array || [],
                callableScope:String = scope || null;

            return call.apply(this, [callable, callableScope].concat(callableArgs));
        }
        
        /**
         * Checks if the current page is using SSL
         * @return Boolean
         * 
         */
        public function isHttps():Boolean {
            return call("function() { return 'https:' == document.location.protocol; }") as Boolean;
        }
        
        /**
         * Getter for the current js interface implementation
         * @return ExternalInterface
         * 
         */
        protected function get jsInterface():ExternalInterface {
            return _jsInterface;
        }
        
        /**
         * Setter for the current js interface implementation
         * @param iface
         * 
         */
        protected function set jsInterface(iface:ExternalInterface):void {
            _jsInterface = iface;
        }
        
        /**
         * Getter for the current configuration
         * @return Configuration
         * 
         */
        protected function get configuration():Configuration {
            return _config;
        }
        
        /**
         * Setter for the current configuration
         * @param config
         * 
         */
        protected function set configuration(config:Configuration):void {
            _config = config;
        }
        
        /**
         * Initialisation method. Sets up the references to the JS interface object, and the current configuration
         * 
         */
        protected function initialize():void {
            jsInterface = new ExternalInterface;
            configuration = Configuration.getInstance();
        }
    }
}