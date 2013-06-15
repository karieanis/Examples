package com.examples.logger {
    /**
     * Basic interface describing the logging methods required by implementations 
     * @author Jeremy Rayner <jeremy@davros.com.au>
     * 
     */
    public interface ILogger {
        
        /**
         * Logging method 
         * @param message
         * @param o
         * 
         */
        function log(message:*, o:* = null):void;
    }
}