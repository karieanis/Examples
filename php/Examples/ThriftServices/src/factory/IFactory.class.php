<?php
namespace Examples\ThriftServices\Factory;

interface IFactory {
    public function register($key, $class);
    public function create($key);
}