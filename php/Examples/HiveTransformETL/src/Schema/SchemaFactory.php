<?php
namespace Examples\HiveTransformETL\Schema;

/**
 * Factory class for generating schema objects
 * @final
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
final class SchemaFactory {
    const IFACE = "\Examples\HiveTransformETL\Schema\ISchemaMap";
    
    /**
     * @codeCoverageIgnore
     */
    protected function __construct() {
        
    }
    
    /**
     * Generate a schema of the passed class type. Ensure that it implements the appropriate schema interfaces prior
     * to manufacture.
     * 
     * @param string $class                            The schema class
     * @throws \Exception
     * @return \Examples\HiveTransformETL\Schema\ISchemaMap    A manufactured schema object
     */
    public static function factory($class) {
        $obj = null;
        
        try {
            $reflector = new \ReflectionClass($class);
            
            if(!$reflector->implementsInterface(static::IFACE)) {
                throw new \Exception(sprintf("Class %s does not implement %s", $class, static::IFACE), 0);
            }
            
            $obj = $reflector->newInstance();
        } catch(\Exception $e) {
            throw new \Exception(sprintf("Unable to construct class %s", $class), 0, $e);
        }
        
        return $obj;
    }
}