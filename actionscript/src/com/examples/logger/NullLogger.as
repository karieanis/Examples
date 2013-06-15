package com.examples.logger {
    /**
     * Null logger implementation
     * @author Jeremy Rayner <jeremy@davros.com.au>
     * 
     */
    public final class NullLogger implements ILogger {
        public function NullLogger() {
        }
        
        /**
         * Null log implementation, NO-OP
         * @param message
         * @param o
         * @return void
         */
        public function log(message:*, o:*=null):void {
            return;
        }
    }
}