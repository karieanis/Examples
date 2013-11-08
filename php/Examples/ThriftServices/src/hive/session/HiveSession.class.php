<?php
namespace Examples\ThriftServices\Hive\Session;

/**
 * Wrapper class for HiveServer2 sessions
 * @author Jeremy Rayner <jeremy@davros.com.au>
 * @codeCoverageIgnore
 */
class HiveSession {
    /**
     * A bytstring representing a guid sent by HiveServer2
     * @var string
     */
    protected $guid;
    /**
     * A bytestring representing the secret sent with the guid
     * @var string
     */
    protected $secret;
    
    /**
     * A log4php logger
     * @var \Logger
     */
    protected static $LOGGER;
    
    /**
     * Constructor
     * @param string $guid
     * @param string $secret
     */
    public function __construct($guid, $secret) {
        $this->setGuid($guid)
            ->setSecret($secret);
    }
    
    /**
     * Generate a Thrift session handler for this HiveSession
     * @return \TSessionHandle
     */
    public function getHandle() {
        $id = new \THandleIdentifier(array('guid' => $this->getGuid(), 'secret' => $this->getSecret()));
        return new \TSessionHandle(array('sessionId' => $id));
    }
    
    /**
     * Get the guid
     * @return string
     */
    protected function getGuid() {
        return $this->guid;
    }
    
    /**
     * Get the secret
     * @return string
     */
    protected function getSecret() {
        return $this->secret;
    }
    
    /**
     * Set the guid
     * @param string $guid
     * @return \Examples\ThriftServices\Hive\Session\HiveSession
     */
    protected function setGuid($guid) {
        $this->guid = $guid;
        return $this;
    }
    
    /**
     * Set the secret
     * @param string $secret
     * @return \Examples\ThriftServices\Hive\Session\HiveSession
     */
    protected function setSecret($secret) {
        $this->secret = $secret;
        return $this;
    }
    
    /**
     * Close this session
     * @param \TCLIServiceIf $service
     */
    public function close(\TCLIServiceIf $service) {
        $request = new \TCloseSessionReq(array('sessionHandle' => $this->getHandle()));
        /* @var \TCloseSessionResp $result */
        $result = $service->CloseSession($request);
    }
    
    /**
     * toString implementation
     * @return string
     */
    public function __toString() {
        return sprintf(__CLASS__ . " [guid=%s]", $this->getGuid());
    }
    
    /**
     * Get a hashed guid
     * @return string
     */
    public function getReadableGuid() {
        return array_shift(unpack("H*", $this->getGuid()));
    }
    
    /**
     * Create a new HiveServer2 session
     * @param \TCLIServiceIf $service
     * @param string $user
     * @param string $password
     * @return \Examples\ThriftServices\Hive\Session\HiveSession
     */
    public static function create(\TCLIServiceIf $service, $user, $password = '') {
        if(is_null(static::$LOGGER)) {
            static::$LOGGER = \Logger::getLogger('ThriftDatabaseLogger');
        }
        
        $request = new \TOpenSessionReq(
            array(
                'client_protocol' => \TProtocolVersion::HIVE_CLI_SERVICE_PROTOCOL_V1,
                'username'        => $user, 
                'password'        => $password
            )
        );
        
        static::$LOGGER->debug("Sending session open request");
        
        /* @var \TOpenSessionResp $result */
        $result = $service->OpenSession($request);
        static::$LOGGER->debug("Session response received, creating session object");
        
        /* @var \TSessionHandle $session */
        $session = $result->sessionHandle;
        
        /* @var \THandleIdentifier $identifier */
        $identifier = $session->sessionId;
        
        $session = new static($identifier->guid, $identifier->secret);
        static::$LOGGER->debug(sprintf("%s: Session obtained", $session));
        return $session;
    }
}