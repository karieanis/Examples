<?php
namespace Examples\Auth\SASL\Mechanism;

/**
 * Factory implementation responsible for manufacturing instances of SASL mechanisms
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
class Factory {
    protected static $ClassMap = array(
        BaseMechanism::ANONYMOUS => "\Examples\Auth\SASL\Mechanism\Anonymous",
        BaseMechanism::PLAIN => "\Examples\Auth\SASL\Mechanism\Plain"
    );
    
    /**
     * Instantiate a mechanism based on the passed mechanism string passed. If we have a mechanism, return it - else
     * return void
     * @static
     * @param string $mechanism
     * @return BaseMechanism|void
     */
    public static function factory($mechanism, $client) {
        $reflector = null;
        $className = isset(static::$ClassMap[$mechanism]) ? static::$ClassMap[$mechanism] : null;

        /* @var $reflector \ReflectionClass */
        if(!is_null($className)) {
            $reflector = new \ReflectionClass($className);
            return $reflector->newInstance($client);
        }
    }
}