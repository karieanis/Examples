<?php
namespace Examples\HiveTransformETL\Schema\Row;

/**
 * Schema class representing the structure of a user agent blacklist rule record
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
class UserAgentBlacklistRule extends \Examples\HiveTransformETL\Schema\AbstractSchemaMap {
    /**
     * 
     * @staticvar array
     */
    protected static $map = array(
        'user_agent',
        'active',
        'exceptions',
        'pass_code',
        'impact_code',
        'match_from_start',
        'inactive_date'
    );
}