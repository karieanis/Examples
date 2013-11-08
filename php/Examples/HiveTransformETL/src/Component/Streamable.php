<?php
namespace Examples\HiveTransformETL\Component;

/**
 * Astract class to represent the logic required of a streamable object
 * @author Jeremy Rayner <jeremy@davros.com.au>
 * @codeCoverageIgnore
 */
abstract class Streamable {
    /**
     * @var string
     */
    protected $stream;

    /**
     * Get the stream
     * @return string
     */
    public function getStream() {
        return $this->stream;
    }

    /**
     * Set the stream
     * @param string $stream
     * @return \Examples\HiveTransformETL\Component\Streamable
     */
    protected function setStream($stream) {
        $this->stream = $stream;
        return $this;
    }
}