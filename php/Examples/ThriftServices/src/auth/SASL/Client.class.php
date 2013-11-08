<?php
namespace Examples\ThriftServices\Auth\SASL;

/**
 * SASL Client interface definition - design has been lifted from javax.security.sasl.SaslClient
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
interface Client {
    /**
     * Return the name of the current mechanism
     * @return string
     */
    public function getMechanismName();
    /**
     * Does the client have an initial response?
     * @return boolean
     */
    public function hasInitialResponse();
    /**
     * Evaluate the incoming challenge from the server, then return the appropriate repsonse. Throws
     * an Exception if an error occurs evaluating the challenge.
     * 
     * @param string $challenge    bitstring
     * @return string              a response bitstring
     * @throws \Examples\ThriftServices\Auth\Sasl\Exception    If an error occurred while processing the challenge 
     *                                                  or generating a response.
     */
    public function evaluateChallenge($challenge = "");
    /**
     * Has the client finished all the authentication steps required of it?
     * @return boolean
     */
    public function isComplete();
    /**
     * Unwrap an incoming message as per the SASL mechanism
     * @param string $incoming    bitstring
     * @return string             outgoing bitstring
     */
    public function unwrap($incoming);
    /**
     * Wrap an outgoing message as per the SASL mechanism
     * @param string $outgoing    bitstring
     * @return string             outgoing bitstring
     */
    public function wrap($outgoing);
    /**
     * Retrieve a property negotiated with the server and return the value
     * @param string $name        Property name
     * @return mixed
     */
    public function getNegotiatedProperty($name);
    /**
     * Dispose of any sensitive information contained within the instance
     * @return void
     */
    public function dispose();
}