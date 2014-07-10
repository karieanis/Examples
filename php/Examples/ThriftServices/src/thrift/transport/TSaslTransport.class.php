<?php
namespace Examples\ThriftServices\Thrift\Transport;

use Thrift\Transport\TTransport,
    Thrift\Exception\TTransportException;

/**
 * Base SASL Transport implementation. Basic design lifted from Thrift.Transport.TSaslTransport and made appropriate for
 * PHP. 
 * 
 * @abstract
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
abstract class TSaslTransport extends TTransport {
    // Expected byte lengths
    
    /**
     * The number of bytes to use for the SASL auth mechanism name
     * @var integer
     */
    const MECHANISM_NAME_BYTES = 1;
    /**
     * The number of bytes to use for the SASL auth status
     * @var integer
     */
    const STATUS_BYTES         = 1;
    /**
     * The number of butes to use for the SASL payload length
     * @var integer
     */
    const PAYLOAD_LENGTH_BYTES = 4;
    
    // SASL Negotition status codes
    
    /**
     * SASL Auth START code
     * @var integer
     */
    const START    = 1;
    /**
     * SASL Auth OK code
     * @var integer
     */
    const OK       = 2;
    /**
     * SASL Auth BAD code
     * @var integer
     */
    const BAD      = 3;
    /**
     * SASL Auth ERROR code
     * @var integer
     */
    const ERROR    = 4;
    /**
     * SASL Auth COMPLETE code
     * @var integer
     */
    const COMPLETE = 5;
    
    /**
     * Map of status codes to human readable strings
     * @var array
     */
    protected static $statusMap = array(
        self::START => "START",
        self::OK => "OK",
        self::BAD => "BAD",
        self::ERROR => "ERROR",
        self::COMPLETE => "COMPLETE"
    );
    
    // Role definitions
    
    /**
     * Constant definition for a concrete class implementing a client transport role
     * @var string
     */
    const ROLE_CLIENT = "CLIENT";
    /**
     * Constant definition for a concrete class implementing a server transport role
     * @var string
     */
    const ROLE_SERVER = "SERVER";
    
    /**
     * The raw transport object
     * @var \Thrift\Transport\TTransport
     */
    protected $transport;
    /**
     * The SASL authentication client
     * @var \Examples\ThriftServices\Auth\SASL\Client
     */
    protected $client;
    
    /**
     * String buffer for readable data
     * @var \Thrift\Transport\TMemoryBuffer
     */
    protected $readBuffer;
    /**
     * String buffer for writeable data
     * @var \Thrift\Transport\TMemoryBuffer
     */
    protected $writeBuffer;
    
    /**
     * log4php Logger object
     * @var \Logger
     */
    protected $logger;
    
    /**
     * Constructor
     * @param \Thrift\Transport\TTransport $transport
     * @param \Examples\ThriftServices\Auth\SASL\Client $client
     */
    public function __construct(\Thrift\Transport\TTransport $transport, \Examples\ThriftServices\Auth\SASL\Client $client = null) {
        $this->transport = $transport;
        $this->client = $client;
        $this->logger = \Logger::getRootLogger();
        
        $this->readBuffer = new \Thrift\Transport\TMemoryBuffer();
        $this->writeBuffer = new \Thrift\Transport\TMemoryBuffer();
    }
    
    /**
     * Concrete classes need to implement this method in order to kickstart the SASL authenication process. The 
     * implementation of this method differs in accordance to the role the concrete class is implementing (CLIENT or 
     * SERVER)
     * 
     * @throws \Thrift\Exception\TTransportException
     * @throws \Examples\ThriftServices\Auth\SASL\Exception
     */
    abstract protected function handleSaslStartMessage();

    /**
     * Return the role that the concrete class is implementing
     * @return string
     */
    abstract protected function getRole();
    
    /**
     * Pack the passed status and payload length into a bytestring header, the write the header and payload
     * using the underlying raw transport.
     * @param int $status
     * @param string $payload
     */
    protected function sendSaslMessage($status, $payload) {
        $length = \Thrift\Factory\TStringFuncFactory::create()->strlen($payload);
        $header = pack("CN*", $status, $length);
        
        $this->logger->debug(sprintf("%s: Writing message with status %s and payload length %d",
                $this->getRole(), static::$statusMap[$status], $length));
        $this->transport->write($header . $payload);
        $this->transport->flush();
    }
    
    /**
     * Read the status and payload length information from the underlying transport, unpack it and throw an exception
     * if an invalid status was returned. Read the payload from the underlying transport in accordance to the determined
     * payload length in the header, then wrap the result in an instance of \Examples\ThriftServices\Auth\SASL\Response
     * 
     * @return \Examples\ThriftServices\Auth\SASL\Response
     * @throws \Thrift\Transport\TTransportException
     */
    protected function receiveSaslMessage() {
        $header = $this->transport->readAll(static::STATUS_BYTES + static::PAYLOAD_LENGTH_BYTES);
        $data = unpack("Cstatus/Nlength", $header);

        // unpack with C will intepret a null byte as 0 in php
        if(!is_null($status = array_shift($data)) && $status === 0) {
            $status = null;
        }

        $length = (is_null($temp = array_shift($data))) ? 0 : $temp;
        $payload = $length > 0 ? $this->transport->readAll($length) : "";

        if(is_null($status)) {
            $this->sendAndThrowMessage(static::ERROR, "Invalid status");
        } else if(in_array($status, array(static::BAD, static::ERROR))) {
            throw new TTransportException("Peer indicated failure: " . $payload, 0);
        }

        $this->logger->debug(sprintf("%s: Received message with status %s and payload length %d",
                $this->getRole(), static::$statusMap[$status], $length));        
        return new \Examples\ThriftServices\Auth\SASL\Response($status, $payload);
    }
    
    /**
     * Send a SASL message, then throw an Exception for the application logic to handle
     * @param int $status
     * @param string $message
     * @throws TTransportException
     */
    protected function sendAndThrowMessage($status, $message) {
        try {
            $this->sendSaslMessage($status, $message);
        } catch(\Exception $e) {
            // @codeCoverageIgnoreStart
            $this->logger->warn("Could not send failure response", $e);
            $message .= PHP_EOL . "Also, could not send response: " . $e->getMessage();
            // @codeCoverageIgnoreEnd
        }
        
        throw new TTransportException($message);
    }
    
    /**
     * Set the logger object for this instance
     * @param \Logger $logger
     * @return \Examples\ThriftServices\Thrift\Transport\TSaslTransport
     */
    public function setLogger(\Logger $logger) {
        $this->logger = $logger;
        return $this;
    }
    
    /**
     * @see \Thrift\Transport\TTransport::open()
     * @codeCoverageIgnore
     */
    public function open() {
        $this->logger->debug(sprintf("opening transport %s", get_class($this)));
        
        // if the SASL client has been set, and indicates that auth negotiation is complete
        if(!is_null($this->client) && $this->client->isComplete()) {
            throw new TTransportException("SASL transport already open");
        }
    
        // if the underlying transport is not open, then open it
        if(!$this->transport->isOpen()) {
            $this->transport->open();
        }
    
        try {
            $message = null;
            $this->handleSaslStartMessage(); // negotiate a SASL mechanism
            $this->logger->debug(sprintf("%s: Start message handled", $this->getRole()));    
            
            while(!$this->client->isComplete()) {
                $message = $this->receiveSaslMessage();

                if(!in_array($message->getStatus(), array(static::COMPLETE, static::OK))) {
                    throw new TTransportException("Expected COMPLETE or OK, got " . static::$statusMap[$message->getStatus()]);
                }
                
                $challenge = $this->client->evaluateChallenge($message->getPayload());
                
                // if this is a client implementation and the server has returned a COMPLETE status
                if($message->getStatus() == static::COMPLETE && $this->getRole() == static::ROLE_CLIENT) {
                    $this->logger->debug(sprintf("%s: All done", $this->getRole()));
                    break;
                }
                
                $this->sendSaslMessage($this->client->isComplete() ? static::COMPLETE : static::OK, $challenge);
            }
            
            $this->logger->debug(sprintf("%s: Main negotiation loop complete", $this->getRole()));
     
            // if this is a client implementation where the mechanism indicates that the negotiation is complete, but
            // we haven't received the COMPLETE response from the server, try to get it.
            if($this->getRole() == static::ROLE_CLIENT &&
                (is_null($message) || $message->getStatus() == static::OK)) {
                
                $this->logger->debug(sprintf("%s: SASL Client receiving last message", $this->getRole()));
                $message = $this->receiveSaslMessage();
                
                if($message->getStatus() !== static::COMPLETE) {
                    throw new TTransportException("Expected SASL COMPLETE, but got " . static::$statusMap[$message->getStatus()], 0);
                }
            }
            // check for complete message from server
        } catch(\Exception $e) {
            $this->logger->error("SASL negotiation failure", $e);
            $this->sendAndThrowMessage(static::BAD, $e->getMessage());
        }
    
        // check QOP
        // do this if we need to do so message wrapping
    }
    
    /* 
     * @see \Thrift\Transport\TTransport::read()
     */
    public function read($len) {
        if(!$this->isOpen()) {
            throw new TTransportException("SASL authentication not complete");
        }
        
        if($this->readBuffer->available() == 0) {
            // @codeCoverageIgnoreStart
            $this->readFrame();
            // @codeCoverageIgnoreEnd
        }

        return $this->readBuffer->read($len);
    }
    
    /**
     * Read a frame from the underlying transport, then write it to the read buffer, unwrapping the frame
     * as per the client mechanism where appropriate
     * @return void
     */
    public function readFrame() {
        $header = $this->transport->readAll(static::PAYLOAD_LENGTH_BYTES);
        $data = unpack("N*", $header);
        $length = array_shift($data);
        
        if($length < 0) {
            // @codeCoverageIgnoreStart
            throw new TTransportException("Read a negative frame size (" . $length . ")!");
            // @codeCoverageIgnoreEnd
        }
    
        $this->logger->debug(sprintf("%s: Reading data length %d", $this->getRole(), $length));
        $encoded = $this->transport->readAll($length);
        $this->readBuffer->write($this->client->unwrap($encoded));
    }
    
    /* 
     * @see \Thrift\Transport\TTransport::write()
     */
    public function write($buff) {
        if(!$this->isOpen()) {
            throw new TTransportException("SASL authentication not complete");
        }
        
        // @codeCoverageIgnoreStart
        $this->writeBuffer->write($buff);
        // @codeCoverageIgnoreEnd
    }
    
    /**
     * @see \Thrift\Transport\TTransport::flush()
     * @codeCoverageIgnore
     */
    public function flush() {
        $encoded = $this->client->wrap($this->writeBuffer->read($this->writeBuffer->available()));
        $length = \Thrift\Factory\TStringFuncFactory::create()->strlen($encoded);
        $header = pack("N*", $length);
        
        $this->logger->debug(sprintf("Writing data length %d", $length));
        
        $this->transport->write($header . $encoded);
        $this->transport->flush();
    }
    
    /**
     * @see \Thrift\Transport\TTransport::close()
     * @codeCoverageIgnore
     */
    public function close() {
        $this->transport->close();
        $this->client->dispose();
    }
    
    /* 
     * @see \Thrift\Transport\TTransport::isOpen()
     */
    public function isOpen() { 
        return $this->transport->isOpen() && $this->client->isComplete();
    }
}