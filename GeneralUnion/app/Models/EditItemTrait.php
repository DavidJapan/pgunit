<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Models;

use Illuminate\Http\Request;
use App\AppClasses\DataControl;

/**
 * Description of EditItemTrait
 * 
 * This trait adds the necessary edit controls and client-side JavaScript scripts to a EditableDataTable model to
 * allow the user to edit a data table.
 *
 * @author David Mann
 */
trait EditItemTrait {

    /**
     * Specifies that you can edit the data table associated with the model that uses
     * this trait.
     * @var bool 
     */
    //protected $can_edit = true;
    //protected $item_editor_form_id = 'item_editor';

    /**
     * The array of definitions of controls which are displayed in the edit item dialog box.
     * @var array
     */
    protected $edit_controls = [];

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
    protected $edit_dialog_width = 12; //uses Bootstrap grid so values like 12, 6, 8 etc are common.
    /**
     * An array of relative links to the extra JavaScript files needed for the client-side
     * functionality.
     * @var array 
     */
    protected $edit_scripts = [
        '/js/crud/selected_item.js',
        '/js/crud/selected_items.js',
        '/js/crud/edit.js'
    ];

        /**
         * Creates a hash map of values to be merged with the view_data array which is passed to the view:
         * <ul>
         * <li>
         * 
         * edit_dialog_width
         * </li>
         * <li>
         * name_field
         * </li>
         * <li>
         * item_name
         * </li>
         * <li>
         * scripts
         * </li>
         * </ul>
         * @var array 
         */
    public function initEditViewData() { 
        $edit_view_data = [
            'edit_dialog_width' => $this->getEditDialogWidth(),
            'scripts' => $this->scripts(),
            'edit_controls' => $this->editControls()
        ];
        return $edit_view_data;
    }

    /**
     * @return string Gets the edit_dialog_width
     * @see edit_dialog_width
     */
    public function getEditDialogWidth() {
        return $this->edit_dialog_width;
    }

    /**
     * @param int $dialog_width
     * Default is zero-length string.
     */
    public function setEditDialogWidth($dialog_width) {
        $this->edit_dialog_width = $dialog_width;
    }

    /**
     * Initialises the edit_controls array by looping through the fillable array for this model snd
     * creating a DataControl for each field. The default DataControl is a textbox.
     * The assumption is that if a field is fillable, some kind of control should be displayed on
     * the edit item dialog box. Individual models which extend EditableDataTable can define exceptions.
     * This function is called in the constructor of a model which uses this trait.
     */
    public function initialiseEditControls() {
        $fillable = $this->getFillable();
        for ($i = 0; $i < count($fillable); $i++) {
            $field_name = $fillable[$i];
            $control = new DataControl($field_name);
            $this->edit_controls[$field_name] = $control;
        }
    }

    /**
     * Gets the edit_controls array.
     * @return array
     */
    public function editControls() {
        return $this->edit_controls;
    }

    /**
     * Merges the array of links to JavaScript files required for the client side
     * to handle editing an item with the links already defined for a data table.
     * @param array $scripts
     * @return array
     */
    public function mergeEditScripts($scripts) {
        return array_merge($scripts, $this->edit_scripts);
    }

    /**
     * Gets the named edit_control
     * @param string $control_name
     * @return DataControl
     */
    public function getEditControl(string $control_name) {
        return $this->edit_controls[$control_name];
    }

    /**
     * Gets the named control and assigns the specified DataControl definition to it.
     * @param string $control_name
     * @param DataControl $control
     */
    public function setEditControl(string $control_name, DataControl $control) {
        $this->edit_controls[$control_name] = $control;
    }

    /**
     * Deletes the named control from the edit_controls array.
     * 
     * @see https://stackoverflow.com/questions/2448964/php-how-to-remove-specific-element-from-an-array/2449093
     * @param string $control_name
     */
    public function deleteEditControl(string $control_name) {
        if (array_key_exists($control_name, $this->edit_controls)) {
            unset($this->edit_controls[$control_name]);
        }
    }

    /**
     * 
     * @param Request $request
     * @param mixed (usually an integer) $id
     * @return string JSON-encoded string representing the model instance
     * just updated.
     */
    public function handleSuccessfulUpdate(Request $request, $directory, $id) {
        $json = new \stdClass();
        $json->data = new \stdClass();
        //We use the instance method here because we want to call the one_function if it exists.
        $json->data->$directory = $this->one([$id]);
        return json_encode($json);
    }

    /**
     * Can't use the name "update" for this method, because the Eloquent model already
     * has an update method with 2 parameters, attributes and options.
     */
    public function updateModel(Request $request, $directory) {
        $primaryKey = $this->primaryKey;
        $current_id = $request->input("current_" . $primaryKey);
        if ($request->exists($primaryKey)) {
            $id = $request->input($primaryKey);
        } else {
            $id = $current_id;
        }
        //We use the static method here because we only
        //want the model's own table's fields, not the fields
        //provide by the model's all_function.
        //We need the current_id to find the correct model instance
        //Then use new value of the primary key for any updates.
        $model_instance = static::find($current_id);
        //Where the primary key is an auto-increment integer field,
        //it won't be fillable, but for other types of primary keys
        //it will be fillable.
        foreach ($this->getFillable() as $field_name) {
            $model_instance->$field_name = $request->input($field_name);
        }
        $result = $model_instance->save();
        if ($result) {
            return $this->handleSuccessfulUpdate($request, $directory, $id);
        } else {
            $error = $result->errors()->all(':message');
            $json = new \stdClass();
            $json->error = $error;
            return json_encode($json);
        }
    }

    //public function itemEditorFormId() {
    //    return $this->item_editor_form_id;
    //}

    //public function setItemEditorFormId($item_editor_form_id) {
    //    $this->item_editor_form_id = $item_editor_form_id;
    //}

}
