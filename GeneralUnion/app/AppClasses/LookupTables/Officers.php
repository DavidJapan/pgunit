<?php

/**
 * Creates a LookupTable based on the users table for use
 * as the options array bound to a select box in a Knockout view model.
 */

namespace App\AppClasses\LookupTables;

/**
 * This gathers a lookup table from the users table, using officer_id as the index
 * and 'officer' and 'officer_id' as the values to display in the select box.
 * @uses PostgreSQL function officers_all_get to personalise the users table, passing the user_id
 * to the more convenient officer_id alias.
 * @author David Mann
 */
class Officers extends LookupTable {
    /**
     * The table users
     * @var string
     */
    protected $table_name = 'users';
    /**
     * The function officers_all_get
     * @var string
     */
    protected $function_name = 'officers_all_get';
    /**
     * Uses officer_id as the value for each item in the drop down box.
     * @var string
     */
    protected $index = 'officer_id';
    /**
     * officer and officer_id
     * @var array
     */
    protected $fields = ['officer', 'officer_id'];
    /**
     * Order by officer
     * @var string
     */
    protected $order_by = 'officer asc';

}
