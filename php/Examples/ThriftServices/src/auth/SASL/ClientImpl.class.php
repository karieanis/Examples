<?php
namespace Examples\ThriftServices\Auth\SASL;

/**
 * Default implementation of a SASL Client
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
class ClientImpl implements Client {
    /**
     * The SASL mechanism
     * @var \Examples\ThriftServices\Auth\SASL\Mechanism\BaseMechanism
     */
    protected $mechanism;
    /**
     * Property collection
     * @var \Examples\ThriftServices\Util\Collection
     */
    protected $properties;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->properties = new \Examples\ThriftServices\Util\Collection();
    }
    
    /**
     * Get the current mechanism used by the client
     * @return \Mechanism\BaseMechanism
     */
    public function getMechanism() {
        return $this->mechanism;
    }
    
    /**
     * Set the current mechanism to be used
     * @param Mechanism\BaseMechanism $mechanism
     * @return \Examples\ThriftServices\Auth\SASL\ClientImpl
     */
    public function setMechanism(Mechanism\BaseMechanism $mechanism) {
        $this->mechanism = $mechanism;
        return $this;
    }
    
    /**
     * @see \Examples\ThriftServices\Auth\SASL\Client::getMechanismName()
     * @codeCoverageIgnore
     */
    public function getMechanismName() {
        return $this->mechanism->getName();
    }
    
    /**
     * @see \Examples\ThriftServices\Auth\SASL\Client::hasInitialResponse()
     * @codeCoverageIgnore
     */
    public function hasInitialResponse() {
        return true;
    }
    
    /**
     * @see \Examples\ThriftServices\Auth\SASL\Client::evaluateChallenge()
     * @codeCoverageIgnore
     */
    public function evaluateChallenge($challenge = "") {
        return $this->mechanism->process($challenge);
    }
    
    /**
     * @see \Examples\ThriftServices\Auth\SASL\Client::isComplete()
     * @codeCoverageIgnore
     */
    public function isComplete() {
        return $this->mechanism->isComplete();
    }
    
    /**
     * @see \Examples\ThriftServices\Auth\SASL\Client::unwrap()
     * @codeCoverageIgnore
     */
    public function unwrap($incoming) {
        return $this->mechanism->unwrap($incoming);
    }
    
    /**
     * @see \Examples\ThriftServices\Auth\SASL\Client::wrap()
     * @codeCoverageIgnore
     */
    public function wrap($outgoing) {
        return $this->mechanism->wrap($outgoing);
    }
    
    /**
     * @see \Examples\ThriftServices\Auth\SASL\Client::getNegotiatedProperty()
     * @codeCoverageIgnore
     */
    public function getNegotiatedProperty($name) {
        return $this->properties->getAt($name);
    }
    
    /**
     * @see \Examples\ThriftServices\Auth\SASL\Client::dispose()
     * @codeCoverageIgnore
     */
    public function dispose() {
        $this->mechanism->dispose();
    }
}