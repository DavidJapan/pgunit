<?php

/**
 * The functions here enable server-side methods to check that the necessary tables 
 * and functions are present on the Postgresql database associated with this application.
 */

namespace App\AppClasses;
use DB;
/**
 * Description of DbMetadata
 * For performance these would be better run as tests during the development stage, but
 * testing is very hard to run smoothly in my environment and, for instance, DB::select 
 * fails silently and triggers a connection reset if the Postgresql function called in the raw SQL
 * is not present in the database, so it's just safer to use these functions to check before attempting
 * to run a raw select query involving Postgresql functions.
 * 
 * @author David Mann
 */
class DbMetadata {

    /**
     * 
     * When calling this function, it's best to pass config('gu.SCHEMA') to this argument and let
     * the application configuration take care of which schema is used.
     * @param string $schema The default schema is public
     * @param string $function The name of the Postgresql function (either sql or plpgsql)
     * @return bool
     */
    public static function functionExists($schema = 'public', $function) {
        $sql = <<<SQL
    SELECT EXISTS(
        SELECT 1
        FROM pg_proc p 
        JOIN pg_namespace n ON p.pronamespace = n.oid 
        WHERE n.nspname = '$schema'
        AND p.proname = '$function'
    )
SQL;
        $return = DB::selectOne($sql, []);
        return $return->exists;
    }

    /**
     * Pass the name of the schema and table to check
     * whether the given table exists in the specified schema.
     * Note the odd use of 1 in the SELECT clause - explained in the comment.
     * When calling this function, it's best to pass config('gu.SCHEMA') to this argument and let
     * the application configuration take care of which schema is used.
     * @link https://stackoverflow.com/questions/20582500/how-to-check-if-a-table-exists-in-a-given-schema
     * @param type $schema
     * @param type $table
     * @return type
     */
    public static function tableExists($schema, $table) {
        //We use SELECT 1 just to check whether there is a row returned
        //by the SELECT query given the conditions (like a WHERE clause)
        //Note the use of the heredoc notation. The final identifier SQL
        //must not be indented.
        $sql = <<<SQL
    SELECT EXISTS (
        SELECT  1 
        FROM    pg_catalog.pg_class c 
        JOIN    pg_catalog.pg_namespace n 
        ON      n.oid = c.relnamespace 
        WHERE   n.nspname = '$schema'
        AND     c.relname = '$table'
        AND     c.relkind = 'r'    -- only tables
    )
SQL;
        $return = DB::selectOne($sql, []);
        return $return->exists;
    }

}
