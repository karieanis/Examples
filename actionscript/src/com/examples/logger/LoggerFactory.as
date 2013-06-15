package com.examples.logger {
    import com.examples.logger.JSConsoleLogger;
    import com.examples.logger.NullLogger;
    
    /**
     * Logger factory class. Currently used for the generation of either a JS logger class or a null logger depending on the passed
     * arguments to the factory method.
     * 
     * @author Jeremy Rayner <jeremy@davros.com.au>
     * 
     */
    public final class LoggerFactory {
        public function LoggerFactory() {
        }
        
        /**
         * Manufacture an ILogger appropriate for the passed arguments.
         * @param _isDebug
         * @param version
         * @return ILogger
         * 
         */
        public static function factory(_isDebug:Boolean, version:String = null):ILogger {
            var logger:ILogger;
            
            if(_isDebug) {
                logger = new JSConsoleLogger(version);
            } else {
                logger = new NullLogger;
            }
            
            return logger;
        }
    }
}