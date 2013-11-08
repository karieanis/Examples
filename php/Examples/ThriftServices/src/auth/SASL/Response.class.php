<?php
namespace Examples\ThriftServices\Auth\SASL;

/**
 * Basic wrapper class for SASL messages
 * @final
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
final class Response {
    /**
     * SASL authentication status (START, OK, BAD, ERROR or COMPLETE)
     * @var mixed
     */
    protected $status;
    /**
     * The SASL message payload
     * @var mixed
     */
    protected $payload;
    
    /**
     * Constructor
     * @param mixed $status
     * @param mixed $payload
     */
    public function __construct($status, $payload) {
        $this->status = $status;
        $this->payload = $payload;
    }
    
    /**
     * Get the status
     * @return integer
     */
    public function getStatus() {
        return $this->status;
    }
    
    /**
     * Get the SASL payload
     * @return mixed
     */
    public function getPayload() {
        return $this->payload;
    }
}