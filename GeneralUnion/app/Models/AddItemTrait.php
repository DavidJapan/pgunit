<?php

namespace App\Models;

use App\AppClasses\DataControl;
use Illuminate\Http\Request;
use App\AppClasses\DataTableModelException;
use DB;

/**
 * Description of AddItemTrait
 * 
 * This trait provides the necessary functions to allow the user to add a new item
 * to a data table.
 * 
 * @author David Mann
 */
trait AddItemTrait {

    /**
     * The array of definitions of controls which are displayed in the add item dialog box.
     * @var array
     */
    protected $add_controls = [];

    /**
     * The width of the panel (in Bootstrap 4 this is a card) used to display the text boxes and other controls
     * the user needs to enter a new item. Uses Bootstrap grid so values like 12, 6, 8 etc are common.:
     * <ul>
     * <li>
     * 12 for the full width of the viewable area.
     * </li>
     * <li>
     * 6 for half the width.
     * </li>
     * <li>
     * etc
     * </li>
     * </ul>
     * @var string 
     */
    protected $add_dialog_width = 12;

    /**
     * An array of relative links to the extra JavaScript files needed for the client-side
     * functionality.
     * @var array 
     */
    protected $add_scripts = [
        '/js/crud/new_item.js',
        '/js/crud/add.js'
    ];
    /**
     * @return string Gets the add_dialog_width
     * @see add_dialog_width 
     */
    public function getAddDialogWidth() {
        return $this->add_dialog_width;
    }

    /**
     * @param string $dialog_width
     * can be 12, 6 or other integer values.
     * Default is zero-length string.
     */
    public function setAddDialogWidth($dialog_width = "") {
        $this->add_dialog_width = $dialog_width;
    }

    /**
     * Initialises the add_controls array by looping through the fillable array for this model snd
     * creating a DataControl for each field. The default DataControl is a textbox.
     * The assumption is that if a field is fillable, some kind of control should be displayed on
     * the add item dialog box. Individual models which extend EditableDataTable can define exceptions.
     * This function is called in the constructor of a model which uses this trait.
     */
    public function initialiseAddControls() {
        $fillable = $this->getFillable();
        for ($i = 0; $i < count($fillable); $i++) {
            $field_name = $fillable[$i];
            $control = new DataControl($field_name);
            $this->add_controls[$field_name] = $control;
        }
    }

    /**
     * Gets the add_controls array.
     * @return array
     */
    public function addControls() {
        return $this->add_controls;
    }

    /**
     * Merges the array of links to JavaScript files required for the client side
     * to handle adding a new item with the links already defined for a data table.
     * @param array $scripts
     * @return array
     */
    public function mergeAddScripts($scripts) {
        return array_merge($scripts, $this->add_scripts);
    }

    /**
     * Gets the name of the PostgreSQL function needed to get one item from the data table
     * represented by this model.
     * @return string
     */
    public static function getOneFunction() {
        return static::$one_function;
    }

    /**
     * This application is built assuming most models use functions in postgresql. However,
     * where a model uses a table, only the primary key is needed. In this case, we
     * assume that the first element in the bindings array will be the primary key.
     * @param array $bindings
     * @return \stdClass
     * @throws DataTableModelException
     */
    public function one($bindings = []) {
        try {
            if (!is_null(static::getOneFunction())) {
                $select = self::getSelectFromBindings(['*'], $bindings, static::getOneFunction());
                $one = DB::select($select, $bindings);
                return collect($one)->first();
            } else {
                return parent::find($bindings[0]);
            }
        } catch (\Exception $e) {
            throw new DataTableModelException("There was a problem trying find one row of this table: " . $e->getMessage());
        }
    }

    /**
     * Get an add control by name.
     * @param string $control_name
     * @return DataControl
     */
    public function getAddControl(string $control_name) {
        return $this->add_controls[$control_name];
    }

    /**
     * Set an add control by finding the specified control and assigning the specified DataControl to it.
     * @param string $control_name
     * @param DataControl $control
     */
    public function setAddControl(string $control_name, DataControl $control) {
        $this->add_controls[$control_name] = $control;
    }
//    public function addItemFormId(){
//        return $this->add_item_form_id;
//    }
//    public function setAddItemFormId($add_item_form_id){
//        $this->add_item_form_id = $add_item_form_id;
//    }
}
