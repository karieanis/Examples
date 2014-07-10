<?php
namespace Examples\ThriftServices\Hive\Conf;

class HiveServer2Conf extends \Examples\ThriftServices\Hadoop\Conf\HadoopDatabaseConf {
    protected function applyServiceConf() {
        foreach($this->getServiceConf() as $key => $value) {
            $this[$key] = $value;
        }
     }
     
     protected function getServiceConf() {
         $conf = $this->getConfig();
         $ServiceConf = $conf::get("app>thrift");
         return $ServiceConf['services']['hive_server_2'];
     }
}