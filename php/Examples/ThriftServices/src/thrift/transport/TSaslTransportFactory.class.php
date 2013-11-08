<?php
namespace Examples\ThriftServices\Thrift\Transport;

/**
 * Factory layer used for wrapping incoming transport objects with an instance of TSaslTransport if appropriate. If
 * SASL authentication is not required, the raw transport is returned.
 * 
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
class TSaslTransportFactory {
    /**
     * Wrap the incoming raw transport in a TSaslTransport if the current mechanism is available. If no wrapping is required,
     * return the raw transport object. Throw an exception if the passed mechanism is not available.
     * 
     * @static
     * @param \Thrift\Transport\TTransport $transport
     * @param string $mechanismName
     * @param string $username
     * @param string $password
     * @return \Thrift\Transport\TTransport
     * @throws \InvalidArgumentException
     */
    public static function factory(\Thrift\Transport\TTransport $transport, $mechanismName, $username, $password) {
        if($mechanismName == \Examples\ThriftServices\Auth\SASL\Mechanism\BaseMechanism::NOSASL) {
            return $transport;
        }

        $sasl = new \Examples\ThriftServices\Auth\SASL\ClientImpl();
        
        if(!is_null($mechanism = \Examples\ThriftServices\Auth\SASL\Mechanism\Factory::factory($mechanismName, $sasl))) {
            $reflector = new \ReflectionClass($mechanism);
            
            if($reflector->hasMethod("setUsername")) {
                $mechanism->setUsername($username);
            }
            
            if($reflector->hasMethod("setPassword")) {
                $mechanism->setPassword($password);
            }
            
            $sasl->setMechanism($mechanism);
        } else {
            throw new \InvalidArgumentException("Mechanism " . $mechanismName . " is not supported", 0);    
        }
        
        $transport = new TSaslClientTransport($transport, $sasl);
        $transport->setLogger(\Logger::getLogger("ThriftDatabaseLogger"));
        return $transport;
    }
}