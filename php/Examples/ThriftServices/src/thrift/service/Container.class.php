<?php
namespace Examples\ThriftServices\Thrift\Service;

/**
 * Container class used to abstract specific thrift service implementations from the client code.
 * 
 * Thrift configurations are fairly uniform. They always contain the following elements:
 * - a Transport configuration
 * - a Protocol configuration and
 * - a Service object which utilises these things
 * 
 * The service layer will differ depending on the IDL you use, but everything else stays the same.
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
class Container {
    /**
     * Transport layer object
     * @var mixed
     */
    protected $transport;
    /**
     * Protocol layer object
     * @var mixed
     */
    protected $protocol;
    /**
     * Service client generated from a Thrift IDL
     * @var mixed
     */
    protected $client;
    
    /**
     * Get the current transport object
     * @return mixed
     */
    public function getTransport() {
        return $this->transport;
    }
    
    /**
     * Get the current protocol object
     * @return mixed
     */
    public function getProtocol() {
        return $this->protocol;
    }
    
    /**
     * Get the current service client
     * @return mixed
     */
    public function getClient() {
        return $this->client;
    }
    
    /**
     * Set the current transport layer object
     * @param $transport
     * @return \Examples\ThriftServices\Thrift\Service\Container
     */
    public function setTransport($transport) {
        $this->transport = $transport;
        return $this;
    }
    
    /**
     * Set the current protocol layer object
     * @param $protocol
     * @return \Examples\ThriftServices\Thrift\Service\Container
     */
    public function setProtocol($protocol) {
        $this->protocol = $protocol;
        return $this;
    }
    
    /**
     * Set the service client generated from a Thrift IDL
     * @param mixed $client
     * @return \Examples\ThriftServices\Thrift\Service\Container
     */
    public function setClient($client) {
        $this->client = $client;
        return $this;
    }
}