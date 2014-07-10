<?php
namespace Examples\ThriftServices\Hadoop\Service;

/**
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
final class HadoopTaskDatabaseProvider {
    /**
     * Get a hadoop database connection for the passed class type
     * 
     * @param \BaseTask $task
     * @param string $key
     * @return \Examples\ThriftServices\Hive\HiveDb
     */
    public static function getConnection(\BaseTask $task, $key = 'em_hive_reduce') {
        \Examples\ThriftServices\Thrift\Service\ThriftTaskServiceProvider::getService($task);
        return \DbManager::getHiveDB($key);
    }
}