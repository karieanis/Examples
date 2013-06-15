<?php
namespace Examples\Thrift\Transport;

use Thrift\Transport\TTransport,
    Thrift\Exception\TTransportException;

/**
 * SASL Transport implementation for the Client role. Basic design lifted from Thrift.Transport.TSaslClientTransport and
 * made appropriate for PHP.
 * 
 * @author Jeremy Rayner <jeremy@davros.com.au>
 * @codeCoverageIgnore
 */
class TSaslClientTransport extends TSaslTransport {
    /* 
     * @see \Examples\Thrift\Transport\TSaslTransport::handleSaslStartMessage()
     */
    protected function handleSaslStartMessage() {
        $initialResponse = ""; // empty initial response
        
        // if the client has an initial response
        if($this->client->hasInitialResponse()) {
            // evaluate it
            $initialResponse = $this->client->evaluateChallenge($initialResponse);
        }
        
        $this->logger->debug(sprintf("Sending mechanism name %s and initial response of length %d",
                $this->client->getMechanismName(), 
                \Thrift\Factory\TStringFuncFactory::create()->strlen($initialResponse)));
        
        $this->sendSaslMessage(static::START, $this->client->getMechanismName()); // send the SASL mechanism name
        $this->sendSaslMessage($this->client->isComplete() ? static::COMPLETE : static::OK, $initialResponse);
    }
    
    /**
     * 
     * @see \Examples\Thrift\Transport\TSaslTransport::getRole()
     */
    protected function getRole() {
        return static::ROLE_CLIENT;
    }
}