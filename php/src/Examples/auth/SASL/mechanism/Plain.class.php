<?php
namespace Examples\Auth\SASL\Mechanism;

/**
 * SASL authentication mechanism for plain text username / password combinations
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
class Plain extends BaseMechanism {
    /**
     * The username used for authentication
     * @var string
     */
    protected $username;
    /**
     * The password used for authentication
     * @var string
     */
    protected $password;
    
    /**
     * Constructor
     * @param \Examples\Auth\SASL\Client $client
     * @param string $username
     * @param string $password
     */
    public function __construct(\Examples\Auth\SASL\Client $client, $username = null, $password = null) {
        parent::__construct($client);
        
        $this->setUsername($username)
            ->setPassword($password);
    }
    
    /* 
     * @see \Examples\Auth\SASL\Mechanism\BaseMechanism::process()
     */
    public function process($challenge = '') {
        $response = "\x00" . $this->getUsername() . "\x00" . $this->getPassword();
        $this->completed = true;
        return $response;
    }
    
    /**
     * Set the username to be used by this mechanism
     * @param string $username
     * @return \Examples\Auth\SASL\Mechanism\Plain
     */
    public function setUsername($username) {
        $this->username = $username;
        return $this;
    }
    
    /**
     * Set the password for this mechanism
     * @param string $password
     * @return \Examples\Auth\SASL\Mechanism\Plain
     */
    public function setPassword($password) {
        $this->password = $password;
        return $this;
    }
    
    /**
     * Get the username to use for this mechanism. If the username property is empty, return credentials
     * for anonymous plain text authentication.
     * 
     * @return string
     */
    public function getUsername() {
        return is_null($this->username) || empty($this->username) ? "anonymous" : $this->username;
    }
    
    /**
     * Get the password to use for this mechanism. If the password property is empty, return credentials
     * for anonymous plain text authentication.
     * 
     * @return string
     */
    public function getPassword() {
        return is_null($this->password) || empty($this->password) ? "anonymous" : $this->password;
    }
    
    /* 
     * @see \Examples\Auth\SASL\Mechanism\BaseMechanism::dispose()
     */
    public function dispose() {
        $this->password = null;
    }
    
    /* 
     * @see \Examples\Auth\SASL\Mechanism\BaseMechanism::getName()
     */
    public function getName() {
        return static::PLAIN;
    }
}