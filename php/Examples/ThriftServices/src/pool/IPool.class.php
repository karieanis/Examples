<?php
namespace Examples\ThriftServices\Pool;

/**
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
interface IPool {
    /**
     * @param mixed $key
     * @return boolean
     */
    public function has($key);
    /**
     * @param mixed $key
     * @param mixed $item
     * @return void
     */
    public function set($key, $item);
    /**
     * @param mixed $key
     * @return mixed
     */
    public function get($key);
    /**
     * @param mixed $key
     * @return void
     */
    public function remove($key);
}