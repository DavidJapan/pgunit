<?php
/**
 * Handles the definition of an object needed for building form with suitable controls to 
 * add or update data.
 */
namespace App\AppClasses;

/**
 * There is only one constructor. The field_name property for this DataControl is required then the properties
 * fillable and display are assumed to be true. If you don't want a control to appear on one of the editing
 * forms, pass false to fillable. The display property refers to the datatable used for displaying the existing data.
 * @author David Mann
 */
class DataControl implements \JsonSerializable {

    /**
     * If a control is required, the client-side program needs to know what the message is
     * and whether edit or add are true. For instance, a control may be required when editing but not 
     * when adding a new item.
     * $required boolean 
     */
    protected $required = false;
    /**
     * Determines whether the control is dealing with a date or not.
     * @var bool 
     */
    protected $moment = false;
    /**
     * The width of the control: 12 is full width, 6 is half the width of the available space.
     * @var int
     */
    protected $width;
    /**
     * The name of the field this control is connected to.
     * @var sting
     */
    protected $name;
    /**
     * The value to use for this control.
     * @var sting
     */
    protected $value;
    /**
     * The type of the input control. The default is textbox.
     * Use the static constants in this class to set this.
     * @var string
     */
    protected $type = self::Textbox;
    /**
     * Determines whether this control only displays the value in the form.
     * @var bool 
     */
    protected $display = true;
    /**
     * Determines whether this control is visible on the form.
     * @deprecated Use display instead
     * @var bool
     */
    protected $visible = true;
    /**
     * The label for this control.
     * @var string
     */
    protected $label;
    /**
     * If set, determines what happens on the client side when the value of the control changes.
     * @var string
     */
    protected $change_event;
    /**
     * Determines whether this control is associated with an array of values.
     * @var bool
     */
    protected $array = false;
    /**
     * Use this when the control should be bound to a knockout observable with a different name to
     * the name value of this control.
     * @var string
     */
    protected $data_bind;
    /**
     * The ID of the template to be used for this control.
     * @var string
     */
    protected $template_id;
    /**
     * Creates a suitably capitalised label from the specified name
     * @param string $name
     */
    public function __construct($name) {
        $this->name = $name;
        $this->label = $this->camelCaseToCapitalised($name);
    }
    /**
     * Setter method for type
     * @param string $type
     */
    public function setType($type) {
        $this->type = $type;
    }
    /**
     * Getter method for type
     * @return string
     */
    public function type(){
        return $this->type;
    }
    /**
     * Setter method for display
     * @param string $display
     */
    public function setDisplay($display) {
        $this->display = $display;
    }
    /**
     * Getter method for display
     * @return string
     */
    public function display() {
        return $this->display;
    }
    /**
     * Setter method for visible
     * @deprecated Use display instead
     * @param bool $visible
     */
    public function setVisible($visible){
        $this->visible = $visible;
    }
    /**
     * Getter method for visible.
     * @deprecated Use display instead.     * 
     * @return bool
     */
    public function visible(){
        return $this->visible;
    }
    /**
     * Setter method for name
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }
    /**
     * Getter method for name
     * @return string
     */
    public function name() {
        return $this->name;
    }
    /**
     * Converts camel case to capitalised 
     * @param string $text
     * @return string
     */
    protected function camelCaseToCapitalised($text) {
        return ucwords(str_replace("_", " ", $text));
    }
    /**
     * The Setter method for value.
     * @param type $value
     */
    public function setValue($value) {
        $this->value = $value;
    }

    /**
     * Value needs to be a hash like this:
     * [
     *      'message'=> 'Please enter a value for first name.'
     * ]
     * @param array $value
     */
    public function setRequired($value) {
        $this->required = $value;
    }
    /**
     * Setter method for moment
     * @param bool $moment
     */
    public function setMoment($moment) {
        $this->moment = $moment;
    }
    /**
     * Setter method for label
     * @param string $label
     */
    public function setLabel($label) {
        $this->label = $label;
    }
    /**
     * Getter method for label
     * @return string
     */
    public function label(){
        return $this->label;
    }
    /**
     * Determines whether to add a change event to this control.
     * @param bool $change_event
     */
    public function setChangeEvent($change_event) {
        $this->change_event = $change_event;
    }
    /**
     * Determines whether a control has an array of values.
     * @param bool $array
     */
    public function setArray($array) {
        $this->array = $array;
    }
    /**
     * Setter method for width
     * @param int $width
     */
    public function setWidth($width){
        $this->width = $width;
    }
    /**
     * Getter method for data_bind
     * @return string
     */
    public function data_bind(){
        return $this->data_bind;
    }
    /**
     * Sets the client-side name to use when binding this control to a Knockout property which
     * has a different name to the name of this data control.
     * @param string $data_bind
     */
    public function setDataBind($data_bind){
        $this->data_bind = $data_bind;
    }
    /**
     * Getter method for template_id
     * @return string
     */
    public function templateId(){
        return $this->template_id;
    }
    /**
     * Setter method for template_id
     * @param string $template_id
     */
    public function setTemplateId($template_id = null){
        $this->template_id = $template_id;
    }
    /**
     * Set to "textbox". This will signal a form builder to add a textbox control
     */
    const Textbox = "textbox";

    /**
     * Set to "selectbox". This will signal a form builder to add a select box
     */
    const SelectBox = "selectbox";

    /**
     * Set to "readonly". This will signal a form builder to create a span element to display some text
     */
    const Readonly = "readonly";

    /**
     * Set to "datapicker". This will signal a form builder to create a jQuery date time picker control that allows
     * the user to choose a date from a calendar.
     */
    const DatePicker = "datepicker";

    /**
     * Set to "textarea". This is an alternative to the TextBox control and will signal a form builder
     * to create a textarea element.
     */
    const TextArea = "textarea";
    /**
     * Set to "toggle". This will signal a form builder to create a toggle control.
     */    
    const Toggle = "toggle";
    /**
     * Set to "display". This will signal a form builder that this control only displays a value.
     */
    const Display = "display";
    /**
     * Returns a hashed array which can be safely encoded as a JSON string.
     * @uses get_object_vars
     * @link https://www.php.net/manual/en/function.get-object-vars.php
     * @return array
     */
    public function jsonSerialize() {
        return get_object_vars($this);        
    }

}
