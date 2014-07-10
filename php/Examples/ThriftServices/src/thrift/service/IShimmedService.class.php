<?php
namespace Examples\ThriftServices\Thrift\Service;

interface IShimmedService {
    /**
     * @return \Examples\ThriftServices\Thrift\Shims\ThriftShim
     */
    public function getShim();
    /**
     * 
     * @param \Examples\ThriftServices\Thrift\Shims\ThriftShim $shim
     * @return \Examples\ThriftServices\Thrift\Service\IShimmedService
     */
    public function setShim(\Examples\ThriftServices\Thrift\Shims\ThriftShim $shim);
}