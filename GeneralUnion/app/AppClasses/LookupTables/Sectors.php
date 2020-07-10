<?php

/**
 * Creates a LookupTable based on the sectors table for use
 * as the options array bound to a select box in a Knockout view model.
 */

namespace App\AppClasses\LookupTables;

/**
 * This gathers a lookup table from the sectors table, using sector_id as the index
 * and sector as the value to display in the select box.
 *
 * @author David Mann
 */
class Sectors extends LookupTable {

    /**
     * Set to sectors
     * @var string
     */
    protected $table_name = 'sectors';
    /**
     * Set to sector_id
     * @var string
     */
    protected $index = 'sector_id';
    /**
     * Only one field: sector
     * @var array
     */
    protected $fields = ['sector'];
    /**
     * Order by sector
     * @var string
     */
    protected $order_by = 'sector asc';

}
