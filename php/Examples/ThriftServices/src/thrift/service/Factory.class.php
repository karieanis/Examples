<?php
namespace Examples\ThriftServices\Thrift\Service;

/**
 * Factory class used to abstract the construction of Thrift Service Container objects form client code. At the moment,
 * this only generates an object based on a pre-defined configuration, however, we should be able to migrate this to a
 * build at runtime model later on without any impact on the client code which depends on the containers it generates.
 * 
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
class Factory {
    /**
     * Manufacture a Thrift Service Container object based on the passed configuration
     * @static
     * @param \Examples\ThriftServices\Hadoop\Conf\HadoopDatabaseConf $configuration
     * @return \Examples\ThriftServices\Thrift\Service\Container
     */
    public static function factory(\Examples\ThriftServices\Hadoop\Conf\HadoopDatabaseConf $configuration) {
        $transportClass = "TSocket";
        $container = new Container();
        
        $hosts = explode(",", $configuration['host']);
        $ports = explode(",", isset($configuration['port']) ? $configuration['port'] : 10000);
        
        if(count($hosts) > 1 || count($ports) > 1) {
            $transportClass = "TSocketPool";
            $resolvedHosts = $resolvedPorts = array();
            
            // build host / port combinations
            if(count($hosts) == 1 && count($ports) > 1) { // single host, multiple ports
                foreach($ports as $port) {
                    array_push($resolvedHosts, current($hosts));
                    array_push($resolvedPorts, $port);
                }    
            } else if(count($hosts) > 1 && count($ports) == 1) { // multiple hosts, single port
                foreach($hosts as $host) {
                    array_push($resolvedHosts, $host);
                    array_push($resolvedPorts, current($ports));
                }
            } else { // multiple hosts and ports
                foreach($hosts as $pos => $host) {
                    array_push($resolvedHosts, $host);
                    array_push($resolvedPorts, isset($ports[$pos]) ? $ports[$pos] : 10000);
                }
            }
            
            $configuration['host'] = $resolvedHosts;
            $configuration['port'] = $resolvedPorts;
        }
        
        $ServiceWrapper = \Examples\ThriftServices\Hadoop\Service\HadoopDatabaseService::getRunningService();
        $transport = $ServiceWrapper->getTransport($transportClass, $configuration);
        $protocol = $ServiceWrapper->getProtocol("TBinaryProtocolAccelerated", $transport);
        
        $ServiceClass = $ServiceWrapper->getServiceClass();
        $client = new $ServiceClass($protocol);
        
        $container->setTransport($transport)
            ->setProtocol($protocol)
            ->setClient($client);
        
        return $container;
    }
}