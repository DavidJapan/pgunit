<?php
/**
 * From version 9.03, I will attempt to follow these naming conventions:
 * https://www.php-fig.org/psr/psr-1/
 * All methods should be camelCase.
 * All private and protected properties will be snake_case
 * All constants will be ALL_CAPS with underscore separators.
 * 
 * All setters will be like this:
 * setPropertyName($value)
 * 
 * In addition:
 * All getters will be camelCase without the word get. For instance, the property 
 * $item_editor_id
 * will have the getter
 * itemEditorId()
 * 
 * A real problem is how to be more consistent with variables sent to the views. Try this:
 * PHP variables sent to the views via the array of view data will use snake_case. JavaScript
 * properties and method are more readable in camelCase so the JavaScript
 * variables that are set based on the PHP variables sent from the server will be camelCase.

 *  * In a model with a name like "Ship" (Capitalised and singular), the Eloquent Model expects to find
 * a table in the database called "ships" (lower case and plural. It even handles less regular plurals like
 * Company - companies. The Eloquent model. If you want to associate a model with a database table whose name
 * doesn't fit this convention, set the protected property $table.
 * 
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\AppClasses\DataTableColumn;
use App\AppClasses\DataTableModelException;
use App\AppClasses\DbMetadata;
use Illuminate\Support\Facades\Schema;
use DB;
use ReflectionClass;

/**
 * 
 * Description of DataTable
 * Extends an Eloquent Model to provide the necessary configuration
 * and data for a client-side jQuery DataTable based on a backend database table.
 * 
 * @author David Mann
 */
class DataTable extends Model {

    /**
     * The default is false because typically we don't want the timestamp mechanism
     * provided by the Eloquent Model.
     * @var bool 
     */
    public $timestamps = false;

    /**
     * We are using columnDefs in the jQuery DataTable so each column definition
     * will need a "targets" property. This is a simple indexed array of such column definitions.
     * @var array 
     */
    protected $columns;

    /**
     * The name of the function used to retrieve one row from the database table
     * @var string
     */
    protected static $one_function;

    /**
     * The name of the function used to retrieve all the rows from the database table
     * @var string
     */
    protected static $all_function;

    /**
     * This is normally automatically generated from the Model's class name by the getItemName
     * method, but it can be set directly by a sub class.
     * @var string 
     */
    protected $item_name;

    /**
     * This is a 2-dimensional array in a format that can be read
     * by a jQuery DataTable constructor as the order property
     * of the options used to initialise the DataTable in this
     * kind of format:
     * 
     * [[1, 'desc'],[2, 'asc']]
     * @var array
     */
    protected $order = [];

    /**
     * Used on the client-side to specify the width of an element. 12 means the element
     * will fill the available space. 6 is half the width etc.
     * @var integer
     */
    protected $width = 12;

    /**
     * The relative path to the view used to display this model.
     * @var string
     */
    protected $view_path = 'tables/single-table';

    /**
     * An array of links to extra JavaScript files required by some models.
     * @var array
     */
    protected $scripts = [];
    /**
     * The array of values we want to send to the client view.
     * @var array 
     */
    protected $view_data = [];

    /**
     * The string to use as header wherever this model is displayed.
     * @var string
     */
    protected $header;

    /**
     * This is set by the DataTableController in the init method.
     * @var string 
     */
    protected $directory;

    /**
     *
     * @var string The name of the field to use when giving an item (i.e. a row) of the
     * data table a description.
     * @example resources/views/elements/editor.blade.php The view used to display the editor
     * dialog box displays the name_field property after the phrase <i>Edit details for</i>:
     * 
     *                     Edit details for 
     *              <!--Make sure name_field is an observable -->
     *              <span  data-bind="text: {{$name_field}}"></span> 
     * 
     * which displays as "Edit details for Company" if the table is companies.
     */
    protected $name_field;

    /**
     * When a table is a mixture of important keys and a lot of extra fields, there
     * are situations where we don't want to display most of the fields.
     * terms_students includes all the comments from the students' reports. When we're assigning students 
     * to classes we only want the students' names. In that situation, we can exclude the table
     * fields and only use the fields defined by the all_function of the model.
     * @var type 
     */
    protected $exclude_table_fields = false;

    /**
     * Calls initialiseFillable and initialiseColumns
     * @param array $attributes
     */
    public function __construct(array $attributes = array()) {
        parent::__construct($attributes);
        $this->initialiseFillable();
        $this->initialiseColumns();
        $this->initHeader();
    }

    /**
     * The client-side is expecting variables with snake_case names
     */
    public function initViewData() {
        $this->view_data = [
            'header' => $this->getHeader(),
            'columns' => $this->columns(),
            'name_field' => $this->name_field,
            'item_name' => $this->getItemName(),
            'data_table_id' => $this->getDataTableId(),
            'primary_key' => $this->primaryKey,
            'directory' => $this->directory,
            'order' => $this->order(), //json_encode($this->order()),
            'width' => $this->width(),
            'scripts' => $this->scripts()
        ];
    }

    /**
     * This data used to be gathered in the controller but almost all the data
     * is available directly from the model so the controller now just invokes this method.
     * @param type $directory
     * @return array
     */
    public function getViewData() {
        $this->view_data['all'] = static::all(); 
        return $this->view_data;
    }

    /**
     * Only works for simple plurals
     */
    private function initHeader() {
        if (is_null($this->header)) {
            $thisClass = new ReflectionClass($this);
            $this->setHeader($thisClass->getShortName() . 's');
        }
    }

    /**
     * This overrides the static function in the Eloquent model because in this app
     * we focus on using functions which return table-like data so that the complexities
     * of the database table relationships are encapsulated in their local environment.
     * This app should just see what feel like tables.
     * @overrides all function in Eloquent Model. That's why
     * it has to be static.
     * @static
     * @param array $columns
     * @param array $bindings
     * @param array $sort_fields
     * @return array
     * @throws DataTableModelException
     */
    public static function all($columns = ['*'], $bindings = [], $sort_fields = []) {
        //throw new DataTableModelException("The database function doesn't exist");
        if (!is_null(static::getAllFunction())) {
            $function = static::getAllFunction();
            $app_name = config('app.name');
            if (DbMetadata::functionExists(config($app_name . '.SCHEMA'), $function)) {
                $select = self::getSelectFromBindings($columns, $bindings, $function, $sort_fields);
                $all = collect(DB::select($select, $bindings));
                return $all;
            } else {
                $e = new DataTableModelException("The database function '" . $function . "' doesn't exist");
                throw $e;
            }
        } else {
            //Primary keys are arrays now, so these static methods don't work
            //The all() method throws an error about an illegal offset.
            //return collect(parent::all());
            $model = new static();
            //Fortunately, we can use an instance of the current model
            //to get the name of the relevant table and do a simple get.
            return DB::table($model->getTable())->get();
        }
    }

    /**
     * Returns the hash map of column definitions as a integer-indexed array
     * suitable for the columns options of a jQuery Datatable.
     * @return array
     */
    public function columns() {
        $columns_array = [];
        foreach ($this->columns as $column) {
            array_push($columns_array, $column);
        }
        return $columns_array;
    }

    /**
     * In the Eloquent model, you can just set fillable as an array of fields. Here, I often
     * use functions to retrieve data from a table and its associated lookup tables so I need 
     * to keep track of both table and function fields for each model. This creates an array
     * of all the fields in the table. It then removes the primary key if the property "incrementing"
     * has been set to true. It also removes the fields "created_at" and "updated_at" if the "timestamps" 
     * property has been set to true.
     */
    protected function initialiseFillable() {
        if (empty($this->getFillable())) {
            $all_fields = $this->getTableFields();
            if ($this->incrementing) {
                $key = array_search($this->getKeyName(), $all_fields);
                unset($all_fields[$key]);
                $all_fields = array_values($all_fields);
            }
            if ($this->timestamps == true) {
                $key = array_search('created_at', $all_fields);
                if ($key) {
                    unset($all_fields[$key]);
                    //https://stackoverflow.com/questions/5217721/how-to-remove-array-element-and-then-re-index-array
                    $all_fields = array_values($all_fields);
                }
                $key = array_search('updated_at', $all_fields);
                if ($key) {
                    unset($all_fields[$key]);
                    $all_fields = array_values($all_fields);
                }
            }
            $this->fillable($all_fields);
        }
    }
    /**
     * Calls getAllFields, loops through the fields and creates a DataTableColumn for each
     * and adds that column to a hashed array using the field name as a key and the DataTableColumn as the value.
     * It increments an index for each column and calls setTargets on the DataTableColumn to set that value.
     * The client-side DataTable requires this targets property.
     */
    protected function initialiseColumns() {
        $fields = $this->getAllFields();
        $this->columns = [];
        $index = 0;
        foreach ($fields as $field) {
            $column = new DataTableColumn();
            $column->setIsPrimaryKey($field == $this->getKeyName());
            $column->setData($field);
            $column->setTitle($this->camelCaseToCapitalised($field));
            $column->setTargets($index);
            $this->columns[$field] = $column;
            $index += 1;
        }
    }

    /**
     * This returns the DataTableColumn stored in the columns hash by passing
     * the specified key. 
     * @param string $column_name
     * @return DataTableColumn
     */
    public function getColumn(string $column_name) {
        if (array_key_exists($column_name, $this->columns)) {
            return $this->columns[$column_name];
        } else {
            return null;
        }
    }

    /**
     * Description of getTableFields
     * 
     * This uses the Schema class and calls getColumnListing to get all the tables in the field.
     * @uses Illuminate\Support\Facades\Schema
     * @return array
     * @throws DataTableModelException
     */
    public function getTableFields() {
        $all_fields = [];
        try {
            $all_fields = Schema::getColumnListing($this->getTable());
        } catch (\Exception $e) {
            throw new DataTableModelException("There was a problem getting the list of columns for " .
                    $this->getTable() .
                    ': ' . $e->getMessage());
        }
        return $all_fields;
    }
    /**
     * I'm pretty sure this is obsolete. It set exclude_table_fields to the specified value.
     * @param type $value
     */
    public function setExcludeTableFields($value) {
        $this->exclude_table_fields = $value;
    }

    /**
     * This checks the value of $exclude_table_fields. If set to the default value of false,
     * the function merges the array of fields gleaned from the table itself and the fields
     * gathered from the function used to gather all records. This uses array_unique to ensure
     * that the resulting array has no duplicate names.
     * If $exclude_table_fields is set to true, only the fields defined in the Postgresql function are gathered.
     * @return array
     */
    public function getAllFields() {
        $fields = [];
        if ($this->exclude_table_fields) {
            $fields = $this->getAllFunctionFields();
        } else {
            $fields = array_merge(
                    $this->getAllFunctionFields(), $this->getTableFields()
            );
        }
        return array_unique(array_values($fields));
    }

    /**
     * Note that we only pass the all_function to this function. It is the responsibility
     * of the developer to ensure that the one_function matches the fields returned by the all_function
     * 
     * This uses this prepared SQL statement:
     * SELECT column_name as name FROM get_function_table_arguments(?,?)
     * to get all the arguments returned by the specified schema and function.
     * @return array
     */
    public function getAllFunctionFields() {
        //return static::$all_function;
        //https://stackoverflow.com/questions/6258333/whats-the-right-way-to-access-static-properties-of-subclasses-in-static-methods
        //self::$property would refer to the parent class's property and static::$property refers to the subclass.
        $all_function_fields = [];
        if (!is_null(static::$all_function)) {
            $select_columns = 'SELECT column_name as name FROM function_table_arguments_all_get(?,?)';
            $app_name = config('app.name');        
            $_all_function_fields = DB::select($select_columns, [config($app_name . '.SCHEMA'), static::$all_function]);
            //I tried to avoid this loop by creating an array directly in postgresql, but postgresql
            //produces an array like this
            //{book_id,isbn} with the extra cdirectoryy brackets and without the necessary quotes 
            foreach ($_all_function_fields as $row) {
                $name = $row->name;
                array_push($all_function_fields, $name);
            }
        } else {
            //For models with only a table and no get functions, the display fields are
            //assumed to be the same as fillable so we just need to call getTableFields
            //I had an infinite loop here. DO NOT call getAllFields here!
            $all_function_fields = $this->getTableFields();
        }
        return $all_function_fields;
    }

    /**
     * This converts camelCase to CAPITALISED.
     * @param type $text
     * @return type
     */
    protected function camelCaseToCapitalised($text) {
        return ucwords(str_replace("_", " ", $text));
    }

    /**
     * columns is a hash table of column names, each assigned to a definition.
     * This sets the definition of the column to the specified column name.
     * @param string $column_name
     * @param DataTableColumn $column
     */
    public function setColumn(string $column_name, DataTableColumn $column) {
        $this->columns[$column_name] = $column;
    }

    /**
     * Generates an ID for the jQuery DataTable table element. It's just the name
     * of the database table associated with this model plus the string "_table".
     * @return string
     */
    public function getDataTableId() {
        return $this->getTable() . '_table';
    }

    /**
     * Returns the configuration of what order to display the DataTable initially.
     * @return array
     */
    public function order() {
        return $this->order;
    }

    /**
     * Returns the value of the width property.
     * @return int
     */
    public function width() {
        return $this->width;
    }

    /**
     * Returns the string representing the relative path to the view used
     * to display this model.
     * @return string
     */
    public function view_path() {
        return $this->view_path;
    }

    /**
     * Sets the name of the Postgresql function used to retrieve
     * one row from the database table.
     * @param string $one_function
     */
    public static function setOneFunction($one_function) {
        static::$all_function = $one_function;
    }

    /**
     * Gets the name of the PostgreSQL function used to retrieve
     * all the rows in the table associated with this model.
     * @return string
     */
    public static function getAllFunction() {
        return static::$all_function;
    }

    /**
     * Sets the name of the Postgresql function used to retrieve
     * all the rows of the database table
     * @param string $all_function
     */
    public static function setAllFunction($all_function) {
        static::$all_function = $all_function;
    }

    /**
     * If $item_name has been left null, this function takes the short name
     * of the model, which is in camel case, and separates it into normal English
     * with spaces between words.
     * @return type
     */
    public function getItemName() {
        if (is_null($this->item_name)) {
            $reflection = new ReflectionClass($this);
            $model_name = $reflection->getShortName();
            return $this->splitNames($model_name);
        } else {
            return $this->item_name;
        }
    }

    /**
     * This function takes a model name like EmailMessage and splits
     * it into two normally spaced English words: Email Message
     * @uses splitCamelCase This is a regexp I found on StackOverflow:
     * @see https://stackoverflow.com/questions/4519739/split-camelcase-word-into-words-with-php-preg-match-regular-expression
     * There's a very helpful tutorial about commenting PHP regular expressions here:
     * https://chromatichq.com/blog/self-documenting-regular-expressions
     * @param string $name
     * @return type
     */
    private function splitNames($name) {
        $re = '/(?#! splitCamelCase Rev:20140412)
        # Split camelCase "words". Two global alternatives. Either g1of2:
        (?<=[a-z])      # Position is after a lowercase,
        (?=[A-Z])       # and before an uppercase letter.
        | (?<=[A-Z])      # Or g2of2; Position is after uppercase,
        (?=[A-Z][a-z])  # and before upper-then-lower case.
        /x';
        $a = preg_split($re, $name);
        return implode(' ', $a);
    }

    /**
     * Sets the initial order of the DataTable
     * @param array $order
     */
    public function setOrder($order) {
        $this->order = $order;
    }

    /**
     * This is the getter for the scripts array,
     * an array of relative paths to any extra JavaScript scripts required
     * by this model.
     * @return array
     */
    public function scripts() {
        return $this->scripts;
    }
    /**
     * Sets the scripts property to the specified value.
     * @param type $scripts
     */
    public function setScripts($scripts) {
        $this->scripts = $scripts;
    }

    /**
     * This function builds an SQL select prepared statement for retrieving data from a PostgreSQL database
     * using an SQL function not a table.  
     * @param array $columns The default is ['*'], which will render an SQL select statement
     * that returns all the columns in the table.
     * @param array $bindings This function builds a prepared statement and generates a comma-separated
     * list of question marks (?) 
     * @param string $function The name of the function in the PostgreSQL database.
     * @param array $sort_fields The fields to be added to an ORDER BY clause.
     * @return string This returns an SQL select prepared statement. Note that this function has
     * no way of validating whether the SQL generated is valid, so functions which use this function
     * are responsible for handling exceptions. 
     */
    public static function getSelectFromBindings($columns = ['*'], $bindings = [], $function, $sort_fields = []) {
        $columns_string = implode(", ", (array) $columns);
        $select = 'select ' . $columns_string . ' FROM ' . $function;
        $select .= '(';
        $bindings_count = count($bindings);
        for ($i = 0; $i < $bindings_count; $i++) {
            if ($i > 0) {
                $select .= ',';
            }
            $select .= '?';
        }
        $select .= ') ';
        return $select;
        if (count($sort_fields) > 0) {
            $select .= 'ORDER BY ';
            foreach ($sort_fields as $field) {
                if ($field !== reset($sort_fields)) {
                    $select .= ',';
                }
                $select .= $field;
            }
        }

        return $select;
    }
    /**
     * Returns the current value of header.
     * @return string
     */
    public function getHeader() {
        return $this->header;
    }
    /**
     * Setter method for header.
     * @param type $header
     */
    public function setHeader($header) {
        $this->header = $header;
    }
    /**
     * Getter method for directory
     * @return string
     */
    public function directory() {
        return $this->directory;
    }
    /**
     * Setter method for directory
     * @param string $directory
     */
    public function setDirectory($directory) {
        $this->directory = $directory;
    }

    /**
     * Returns the name_field for this model.
     * @see name_field
     * @return string
     */
    public function nameField() {
        return $this->name_field;
    }

    /**
     * Sets the name_field for this model.
     * @see name_field
     * @param string $name_field
     */
    public function setNameField($name_field) {
        $this->name_field = $name_field;
    }
    /**
     * Populates a hash of values from the table
     * using primary key values to create a key for each row.
     * @param array $raw_results
     * @param string $key
     * @return \stdClass
     */
    public function populateHash($raw_results, $key) {
        $hash = new \stdClass();
        $test = [];
        $key_array = explode('---', $key);
        foreach ($raw_results as $row) {
            $key_values = [];
            foreach ($key_array as $sub_key) {
                if (array_key_exists($sub_key, $row)) {
                    array_push($key_values, $row->$sub_key);
                }else{
                    array_push($key_values, $sub_key . ' is null');
                }
            }
            $key_value = implode('---', $key_values);
            $hash->$key_value = $row;
            //array_push($test, $key_value);
        }
        return $hash;
    }
    /**
     * Gets a hash of all the values in the database table.
     * @param type $bindings
     * @param type $key
     * @return \stdClass
     */
    public function getAllHash($bindings = [], $key) {
        $hash = new \stdClass();
        $count = 0;
        if ($this->getAllFunction()) {
            $select = $this->getSelectFromBindings(['*'], $bindings, $this->getAllFunction());
            if (isset($this->order_by)) {
                $select .= ' ORDER BY ' . $this->order_by;
            }
            $raw_results = DB::select($select, $bindings);
            //$count = count($raw_results);
            $hash = $this->populateHash($raw_results, $key);
        } elseif ($this->getTable()) {
            $select = 'SELECT * FROM ' . $this->table;

            if (isset($this->order_by)) {
                $select .= ' ORDER BY ' . $this->order_by;
            }
            $select .= ';';
            $results = DB::select($select);
            //$hash = $this->populateHash($results, $key);
        }
        $response = new \stdClass();
        $response->count = $count;
        $response->data = $hash;
        return $response;
    }

}
