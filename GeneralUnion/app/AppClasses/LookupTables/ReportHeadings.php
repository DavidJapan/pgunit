<?php

/**
 * Creates a LookupTable based on the report_headings table for use
 * as the options array bound to a select box in a Knockout view model.
 */

namespace App\AppClasses\LookupTables;

/**
 * This gathers a lookup table from the sectors report_headings, using report_heading_id as the index
 * and report_heading as the value to display in the select box.
 *
 *
 * @author David Mann
 */
class ReportHeadings extends LookupTable {

    /**
     * report_headings
     * @var string 
     */
    protected $table_name = 'report_headings';
    /**
     * report_heading_id
     * @var string
     */
    protected $index = 'report_heading_id';
    /**
     * report_heading
     * @var array
     */
    protected $fields = ['report_heading'];
    /**
     * report_heading asc
     * @var string
     */
    protected $order_by = 'report_heading asc';

}
