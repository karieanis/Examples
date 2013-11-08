<?php
namespace Examples\ThriftServices\Auth\SASL\Mechanism;

/**
 * Abstract mechanism class, should be extended be all concrete implementations
 * @author Jeremy Rayner <jeremy@davros.com.au>
 * @codeCoverageIgnore
 */
abstract class BaseMechanism {
    /**
     * 
     * @var string
     */
    const ANONYMOUS = "ANONYMOUS";
    /**
     * 
     * @var string
     */
    const PLAIN     = "PLAIN";
    /**
     * 
     * @var string
     */
    const NOSASL    = "NOSASL";
    
    
    /**
     * The client object using the mechanism
     * @var \Examples\ThriftServices\Auth\SASL\Client
     */
    protected $client;   
    /**
     * Completion flag
     * @var boolean
     */
    protected $completed = false;
    
    /**
     * Process the incoming challenge and return an appropriate response message
     * @param string $challenge
     * @return string
     */
    abstract public function process($challenge = '');
    /**
     * Dispose of any sensitive information contained within the class properties
     * @return void
     */
    abstract public function dispose();
    /**
     * Return the name of the current mechanism
     * @return string
     */
    abstract public function getName();
    
    /**
     * Constructor
     * @param \Examples\ThriftServices\Auth\SASL\Client $client
     * 
     */
    public function __construct(\Examples\ThriftServices\Auth\SASL\Client $client) {
        $this->client = $client;
    }
    
    /**
     * Default implementation of wrapping an outgoing message
     * @param string $outgoing
     * @return string
     */
    public function wrap($outgoing) {
        return $outgoing;
    }
    
    /**
     * Default implementation for unwrapping an incoming message
     * @param string $incoming
     * @return string
     */
    public function unwrap($incoming) {
        return $incoming;
    }
    
    /**
     * Check if the mechanism has completed
     * @return boolean
     */
    public function isComplete() {
        return $this->completed;
    }
}