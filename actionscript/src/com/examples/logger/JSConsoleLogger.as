package com.examples.logger {
    import com.examples.utils.BrowserProxy;
    
    /**
     * Basic logger class which handles the interaction between a plugin application and the js console for the browser for
     * the purposes of logging debugging information.
     * 
     * @author Jeremy Rayner <jeremy@davros.com.au>
     * 
     */
    public final class JSConsoleLogger implements ILogger {
        private var _version:String     = "";
        private var _proxy:BrowserProxy = new BrowserProxy;
        
        /**
         * Constructor
         * @param version       The version number of the plugin
         * 
         */
        public function JSConsoleLogger(version:String) {
            this.version = version;
        }

        /**
         * Log a message and optional data to the js console for the browser
         * @param message
         * @param o
         * @return void
         */
        public function log(message:*, o:*=null):void {
            var params:Array = [message];
            
            if(null !== o) {
                params.push(o);
            }
            
            logToConsole.apply(null, params);
        }
        
        /**
         * Setter for version
         * @param v
         * @return void 
         */
        internal function set version(v:String):void {
            _version = v;
        }
        
        /**
         * Utilises the browser proxy to execute an anonymous function which logs to the js console within the browser
         * (if available).
         * @return void
         */
        private function logToConsole():void {
            var args:Array = arguments || [],
                js:String = (
                    <![CDATA[
                        function() { 
                            if(window['console']) {
                                window['console'].log.apply(this, arguments); 
                            }
                        }
                    ]]>
                ).toString();
            
            args.unshift(js);
            _proxy.call.apply(null, args);
        }
    }
}