<?php
namespace Examples\Auth\SASL\Mechanism;

/**
 * Anonymous SASL authentication mechanism implementation
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
class Anonymous extends BaseMechanism {
    /**
     * @see \Examples\Auth\SASL\Mechanism\BaseMechanism::process()
     */
    public function process($challenge = "") {
        $response = "Anonymous, None";
        $this->completed = true;
        return $response;
    }
    
    /**
     * @see \Examples\Auth\SASL\Mechanism\BaseMechanism::dispose()
     * @codeCoverageIgnore
     */
    public function dispose() {
        // do nothing
    }
    
    /**
     * @see \Examples\Auth\SASL\Mechanism\BaseMechanism::getName()
     * @codeCoverageIgnore
     */
    public function getName() {
        return static::ANONYMOUS;
    }
}