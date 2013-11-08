<?php
namespace Examples\HiveTransformETL\Conf;

/**
 * Interface used to describe configurable classes
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
interface IConfigurable {
    /**
     * Get the configuration
     * @return BaseConf
     */
    public function getConf();
    /**
     * Set the configuration
     * @param BaseConf $conf
     * @return \Examples\HiveTransformETL\Conf\IConfigurable
     */
    public function setConf(BaseConf $conf);
}