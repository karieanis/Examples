<?php
namespace Examples\ThriftServices\Conf;

/**
 * 
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
interface IConfigurable {
    /**
     * @return BaseConf
     */
    public function getConf();
    /**
     * 
     * @param BaseConf $conf
     * @return \Examples\ThriftServices\Conf\IConfigurable
     */
    public function setConf(BaseConf $conf);
}