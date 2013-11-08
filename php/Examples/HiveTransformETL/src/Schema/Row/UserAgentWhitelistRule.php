<?php
namespace Examples\HiveTransformETL\Schema\Row;

/**
 * Schema class representing the structure of a user agent whitelist rule
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
class UserAgentWhitelistRule extends \Examples\HiveTransformETL\Schema\AbstractSchemaMap {
    /**
     * 
     * @staticvar array
     */
    protected static $map = array(
        'user_agent',
        'active',
        'match_from_start',
        'inactive_date'
    );
}