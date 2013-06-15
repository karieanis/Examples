package com.examples.utils.url {
    /**
     * Utility class used to parse query string style strings
     * @author Jeremy Rayner <jeremy@davros.com.au>
     * 
     */
    public final class Parser {
        public function Parser() {
        }
        
        
        /**
         * Parse the passed query string and return an object containing key value pairs
         * @param kvs       query string of key value pairs
         * @return Object
         * 
         */
        public static function parse(kvs:String):Object {
            var urlParams:Array = kvs.split("&"),
                i:Number, result:Object = {};
            
            for(i = 0; i < urlParams.length; i++) {
                var kvPair:Array = urlParams[i].split("=");
                result[kvPair[0]] = kvPair[1];
            }
            
            return result;
        }
    }
}