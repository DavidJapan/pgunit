<?php
/**
 * An abstract class used as the parent of a series of classes which create lookup tables
 * to be used as the array bound to Knockout select boxes.
 */
namespace App\AppClasses\LookupTables;

use DB;

/**
 * A LookupTable needs to know the underlying database table it draws its data from, but we can also
 * supply a PostgreSQL function that massages the table into a more convenient form for the
 * given select box we want to display.
 *
 * @author David Mann
 */
abstract class LookupTable {

    /**
     * The name of the table in the database
     * @var string
     */
    protected $table_name;

    /**
     * Should not include the double brackets
     * $var string 
     */
    protected $function_name;
    /**
     * The name of the field used to supply the value to the optionsValue 
     * property of a Knockout select box.
     * @var string
     */
    protected $index;
    /**
     * The array of fields we want to display as text in the Knockout select box.
     * @var array
     */
    protected $fields = [];
    /**
     * Builds a select query to be passed to the DB::select method.
     * If $where is specified, it can be a single string or an array of strings.
     * If a function name has been specified, this function builds a prepared statement
     * and passes suitable values to the binding parameter. If we are just using the table
     * directly, this function builds the necessary where clause.
     * @param string|array $where
     * @return string
     */
    public function buildSelect($where = null) {

        $select = 'SELECT ' . $this->index;
        foreach ($this->fields as $field) {
            $select .= ',' . $field;
        }
        if (isset($this->function_name)) {
            $select .= ' FROM ' . $this->function_name . '(';
            if (!is_null($where)) {
                if (!is_array($where)) {
                    $select .= $where;
                } else {
                    $bindings_count = count($where);
                    for ($i = 0; $i < $bindings_count; $i++) {
                        if ($i > 0) {
                            $select .= ',';
                        }
                        $select .= '?';
                    }
                }
            }
            $select .= ')';
        } else {
            $select .= ' FROM ' . $this->table_name . '';
            if (!is_null($where)) {
                $select .= ' WHERE ' . $where;
            }
        }
        if (isset($this->order_by)) {
            $select .= ' ORDER BY ' . $this->order_by;
        }
        $select .= ';';
        return $select;
    }

    /**
     * Calls buildSelect to create a suitable select query string or prepared statement
     * and calls DB::select to get the results.
     * @param string|array $where
     * @return array
     */
    public function get($where = null) {
        $select = $this->buildSelect($where);
        if (is_array($where)) {
            $raw_results = DB::select($select, $where);            
        } else {
            $raw_results = DB::select($select, []);
        }
        $results = [];
        $index = $this->index;
        foreach ($raw_results as $row) {
            $record = new \stdClass();
            $record->$index = $row->$index;
            foreach ($this->fields as $field) {
                $record->$field = $row->$field;
            }
            array_push($results, $record);
        }
        return $results;
    }
    /**
     * Builds an stdClass object from the data gathered from the database, using the field
     * specified by $key as the key to the hash.
     * @param type $where
     * @param type $key
     * @return \stdClass
     */
    public function getHash($where = null, $key){
        $select = $this->buildSelect($where);
        //return $select;
        if (is_array($where)) {
            $raw_results = DB::select($select, $where);            
        } else {
            $raw_results = DB::select($select, []);
        }
        $results = [];
        $index = $this->index;
        foreach ($raw_results as $row) {
            $record = new \stdClass();
            $record->$index = $row->$index;
            foreach ($this->fields as $field) {
                $record->$field = $row->$field;
            }
            $key_value = $row->$key;
            $results[$key_value] = $record;
        }
        return $results;        
    }
}
