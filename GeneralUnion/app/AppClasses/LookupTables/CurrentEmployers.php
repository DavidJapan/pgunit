<?php

/**
 * Creates a lookup table for all employers in the database.
 */

namespace App\AppClasses\LookupTables;

/**
 * Description of CurrentEmployers
 * Creates a lookup table from the employers table using employer_id as the key and displaying the employer field.
 * @uses Postgresql function  employers_current_get
 * @author David Mann
 */
class CurrentEmployers extends LookupTable {

    /**
     * The name of the database table
     * @var string 
     */
    protected $table_name = 'employers';
    /**
     * The name of the PostgreSQL function to use to get the employers.
     * @var string 
     */
    protected $function_name = 'employers_current_get';
    /**
     * The field to use as the key.
     * @var string
     */
    protected $index = 'employer_id';
    /**
     * The fields to use to display values in a select box
     * @var array
     */
    protected $fields = ['employer'];
    /**
     * Order by employer
     * @var string
     */
    protected $order_by = 'employer asc';

}
