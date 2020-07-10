<?php
/**
 * This creates an object that can be encoded as a JSON string to send to the client
 * as an element in an array of columns used by a jQuery Data Table.
 */

namespace App\AppClasses;

/**
 * Defines one column of a Datatable to be displayed in a view
 * using a jQuery Datatable. It creates an array that can be encoded as a JSON string to send to the client
 * as an element in an array of columns used by a jQuery Data Table.
 * 
 * @implements JsonSerializable. This allows me to create an instance of this class in a form
 * easily transferred to the client
 * @link https://stackoverflow.com/questions/9896254/php-class-instance-to-json
 *
 * @author David Mann
 */
class DataTableColumn implements \JsonSerializable {

    /**
     * Signals the Datatable to display a textbox
     */
    const Textbox = "textbox";

    /**
     * This is an alternative to the TextBox control and will signal the Datatable 
     * to display a textarea.
     */
    const TextArea = "textarea";

    /**
     * Signals the Datatable to display the text for this column in a simple td cell.
     */
    const Display = "display";

    /**
     * Determines whether a column is the primary key of a table. Default
     * is false because only one column can be the primary key.
     * @var bool
      https://stackoverflow.com/questions/45348751/argument-x-passed-to-y-must-be-an-instance-of-boolean-boolean-given-php7/45348752
     */
    protected $is_primary_key = false;

    /**
     * The text which is displayed in the column header
     * @var string
     */
    protected $title;

    /**
     * Determines whether the column is displayed
     * @var bool
     */
    protected $visible = true;

    /**
     * The client-side id or the html template used to display
     * each cell in this column. Typically used to display the primary key column in 
     * an editable table with a delete icon and an editing icon which become visible when
     * a row is selected.
     * @var String
     */
    protected $template_id = null;

    /**
     * Determines whether this field should be treated as a date and formatted
     * using the moment library.
     * @var boolean
     */
    protected $moment = false;

    /**
     * Careful! This stores the name of the field that contains the data for this column.
     * It doesn't store the actual data for the column.
     * @var string 
     */
    protected $data;

    /**
     * We're using columnDefs instead of columns for DataTable options so we need
     * to include targets. For now, just assign the column index to targets. The columns
     * array is zero-based.
     * A jQuery Data Table throws a very unpleasant and obscure error message if
     * the targets property isn't set correctly. This is a priority property.
     * @var integer 
     */
    protected $targets;

    /**
     * Sets the width of a column in the data table with a value like
     * 25%, 50em or 100px.
     * The default value is null and this will allow the client-side data table to set the column
     * width automatically.
     * @var String
     */
    protected $width = null;

    /**
     * Determines whether a given field should be treated as JSON. If so, it typically
     * has to be decoded and converted to a string before being re-encoded 
     * @var bool
     */
    protected $is_json = false;
    
    /**
     * Determines whether the field has an array of values.
     * @default false
     * @var bool
     */
    protected $is_array = false;

    /**
     * The type of control which should be displayed. This is usually
     * plain text displayed inside a td cell, but it may be a textbox or textarea.
     * @var string
     */
    protected $display_type = self::Display;
    /**
     * Determines whether this column can be sorted
     * @var bool
     */
    protected $orderable = true;

    /**
     *
     * Typically the name of this field followed by the word Changed
     * in camel case.
     * @var string
     */
    protected $change_event;
    /**
     * The type of display - typically a plain text value
     * @var string
     */
    protected $type;

    /**
     * The Getter method for type
     * @return string
     */
    public function type() {
        return $this->type;
    }
    /**
     * The Setter method for type
     * @param string $type
     */
    public function setType($type){
        $this->type = $type;
    }
    /**
     * This sets the text which is displayed as a header at the top of the column.
     * @param string $title
     */
    public function setTitle(string $title) {
        $this->title = $title;
    }

    /**
     * This sets the client-side id of the HTML element used to display the
     * cell in each row of this column. This is typically used for the primary key
     * column in an editable table which displays a delete button and an edit button when
     * a row is selected.
     * @param string $id
     */
    public function setTemplateId(string $id = null) {
        $this->template_id = $id;
    }

    /**
     * This gets the client-side id of the template used to render this column.
     * @return string
     */
    public function templateId() {
        return $this->template_id;
    }

    /**
     * This sets the width at which this column will be displayed. Normal
     * CSS values can be used like 25%, 20em or 150px,
     * @param string $width
     */
    public function setWidth(string $width) {
        $this->width = $width;
    }

    /**
     * Gets the width at which this column should be displayed.
     * @return sting
     */
    public function width() {
        return $this->width;
    }

    /**
     * Sets whether this column's value should be treated as a JSON object.
     * @param bool $is_json
     */
    public function setIsJson(bool $is_json) {
        $this->is_json = $is_json;
    }

    /**
     * Gets whether this column's value should be treated as a JSON object.
     * @return bool
     */
    public function isJson() {
        return $this->is_json;
    }
    /**
     * Setter method for is_array
     * @param bool $is_array
     */
    public function setIsArray(bool $is_array){
        $this->is_array = $is_array;
    }
    /**
     * Getter method for is_array
     * @return bool
     */
    public function isArray(){
        return $this->is_array;
    }
    /**
     * Gets the text used to display the header of this column.
     * @return string
     */
    public function title() {
        return $this->title;
    }

    /**
     * Careful! Gets the name of the field that contains the data for this column.
     * It doesn't get the actual data for the column.
     * @return string The name of the field which stores the data for this column.
     */
    public function data() {
        return $this->data;
    }

    /**
     * The Getter method for visible.
     * @return bool
     */
    public function visible() {
        return $this->visible;
    }
    /**
     * The Getter method for display_type
     * @return type
     */
    public function displayType() {
        return $this->display_type;
    }
    /**
     * The Getter method for change_event
     * @return string
     */
    public function changeEvent() {
        return $this->change_event;
    }
    /**
     * Setter method for visible
     * @param bool $visible
     */
    public function setVisible(bool $visible) {
        $this->visible = $visible;
    }
    /**
     * Setter method for moment
     * @param bool $moment
     */
    public function setMoment(bool $moment) {
        $this->moment = $moment;
    }

    /**
     * Careful! This does not refer to the actual data stored in a given row. This sets
     * the name of the field which stores the data for this column.
     * @param string $data
     */
    public function setData(string $data) {
        $this->data = $data;
    }
    /**
     * A jQuery Data Table throws a very unpleasant and obscure error message if
     * the targets property isn't set correctly. This is a priority property.
     * @param int $index
     */
    public function setTargets(int $index) {
        $this->targets = $index;
    }
    /**
     * Setter method for change_event
     * @param string $change_event
     */
    public function setChangeEvent(string $change_event) {
        $this->change_event = $change_event;
    }
    /**
     * Setter method for is_primary_key
     * @param bool $is_primary_key
     */
    public function setIsPrimaryKey(bool $is_primary_key) {
        $this->is_primary_key = $is_primary_key;
    }

    /**
     * Getter method for is_primary_key
     * @return bool
     */
    public function isPrimaryKey() {
        return $this->is_primary_key;
    }

    /**
     * Getter method for orderable
     * @return bool
     */
    public function orderable() {
        return $this->orderable;
    }
    /**
     * Setter method for orderable
     * @param bool $value
     */
    public function setOrderable($value) {
        $this->orderable = $value;
    }
    /**
     * Very simple implementation of jsonSerialize. This simply calls
     * get_object_vars on this instance.
     * @return array
     */
    public function jsonSerialize() {
        return get_object_vars($this);
    }

}
