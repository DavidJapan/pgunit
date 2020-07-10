<?php

/**
 * Extends DataControl to provide the extra properties which need to be defined for
 * a select box, such as options, optionsText etc.
 */

namespace App\AppClasses;
//https://stackoverflow.com/questions/11741302/how-do-i-put-blocks-of-php-code-into-a-phpdoc-docblock
/**
 * SelectBoxControl extends DataControl to add the necessary attributes for a select box bounds
 * to an array of options in a Knockout view model.
 * 
 * The data-bind attribute of a select box in a Knockout js view model looks like this:
 * ```
 *   <select class="form-control" data-bind="
 *           attr:{id: mode + '_' + control.name},
 *           options: $root[control.options],
 *           optionsText: function(item){
 *               var key = control.optionsText;
 *               return item[key]
 *           },
 *           optionsValue: function(item){
 *               var key = control.optionsValue;
 *               return item[key]
 *           },
 *           value: value,
 *           optionsCaption: control.optionsCaption"
 *           >
 *     </select>
 * ```
 * So, SelectBoxControl add options, optionsText, optionsValue and optionsCaption. It sets the type
 * of control to selectbox.
 * Inline select boxes have one other complication in this Knockout-enabled application, so if this
 * control is used inline in a data table, we also need to set the displayValue.
 * @extends DataControl
 * @author David Mann
 */
class SelectBoxControl extends DataControl {

    /**
     * selectbox
     * @var string
     */
    protected $type = parent::SelectBox;
    /**
     * The array of value to display in the drop down list.
     * @var array
     */
    public $options;
    /**
     * The field which provides the text to display in the drop down list.
     * @var string
     */
    public $optionsText;
    /**
     * The field which provides the value to assign to each item in the drop down list.
     * @var string
     */
    public $optionsValue;
    /**
     * The caption to display when prompting the user to make a selection.
     * @var string
     */
    public $optionsCaption;

    /**
     * When used inline, the field we use to get and set the value
     * for this Knockout binding may be different from the field
     * we use to display the user's selection.
     * @var string 
     */
    public $displayValue;

    /**
     * The Setter method for options.
     * @param type $options
     */
    public function setOptions($options) {
        $this->options = $options;
    }
    /**
     * The Setter method for optionsText
     * @param type $optionsText
     */
    public function setOptionsText($optionsText) {
        $this->optionsText = $optionsText;
    }
    /**
     * The Setter method for optionsValue
     * @param type $optionsValue
     */
    public function setOptionsValue($optionsValue) {
        $this->optionsValue = $optionsValue;
    }
    /**
     * The Setter method for displayValue
     * @param type $displayValue
     */
    public function setDisplayValue($displayValue) {
        $this->displayValue = $displayValue;
    }
    /**
     * The Setter method for optionsCaption
     * @param type $optionsCaption
     */
    public function setOptionsCaption($optionsCaption) {
        $this->optionsCaption = $optionsCaption;
    }

}
