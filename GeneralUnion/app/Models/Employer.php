<?php
/**
 * This extends EditableDataTable, sets some key values like primaryKey and initialises
 * the three columns employer_id, employer and archived
 */

namespace App\Models;
use App\AppClasses\DataControl;

/**
 * Description of Employer. The model used to administer the employers table.
 *
 * @author David Mann
 */
class Employer extends EditableDataTable {

    /**
     * employer_id
     * @var string
     */
    protected $primaryKey = 'employer_id';
    /**
     * employer
     * @var string
     */
    public $name_field = 'employer';
    /**
     * 6
     * @var int
     */
    protected $width = 6;
    /**
     * [[2, 'asc']]
     * @var array (2-dimensional array)
     */
    public $order = [[2, 'asc']];
    /**
     * Initialises the columns
     * @param array $attributes
     */
    public function __construct(array $attributes = array()) {
        parent::__construct($attributes);
        $this->initEmployerId();
        $this->initEmployer();
        $this->initArchived();
    }
    /**
     * Initialises employer_id
     */
    private function initEmployerId() {
        $column_name = 'employer_id';
        $column = $this->getColumn($column_name);
        $this->setColumn($column_name, $column);
    }
    /**
     * Initialises employer
     */
    private function initEmployer() {
        $column_name = 'employer';
        $control = $this->getAddControl($column_name);
        $control->setRequired([
            'message' => 'Please enter a name for the employer.'
        ]);
        $this->setAddControl($column_name, $control);

        $control = $this->getEditControl($column_name);
        $control->setRequired([
            'message' => 'Please enter a name for the employer.'
        ]);
        $this->setEditControl($column_name, $control);
    }
    /**
     * Initialises archived
     */
    private function initArchived() {
        $column_name = 'archived';
        $control = $this->getAddControl($column_name);
        $control->setType(DataControl::Toggle);
        $this->setAddControl($column_name, $control);
        $this->setEditControl($column_name, $control);
    }

}
