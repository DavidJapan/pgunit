<?php
/**
 * This extends EditableDataTable, sets some key values like primaryKey and initialises
 * the columns in the roles table. Note that $timestamps is set to true so the field created_at and updated_at are required.
 */
namespace App\Models;

use App\AppClasses\DataControl;

/**
 * The model used to administer the roles table.
 */
class AdministerRole extends EditableDataTable {

    /**
     * roles
     * @var string
     */
    protected $table = 'roles';
    /**
     * administer_roles
     * @var string
     */
    public $directory = 'administer_roles';
    /**
     * name
     * @var string
     */
    public $name_field = 'name';
    /**
     * Roles
     * @var string
     */
    public $header = 'Roles';
    /**
     * 6 - half the width of the available space
     * @var int
     */
    public $width = 6;
    /**
     * set to true
     * @var bool 
     */
    public $timestamps = true;
   /**
    * Initialises id, name, created_at and updated_at. Sets the edit and add
    * dialog box widths to 6.
    * @param array $attributes
    */ 
    public function __construct(array $attributes = array()) {
        parent::__construct($attributes);
        $this->initId();
        $this->initName();
        $this->initCreatedAt();
        $this->initUpdatedAt();
        $this->setAddDialogWidth(6);
        $this->setEditDialogWidth(6);
    }
    /**
     * Initialised id
     */
    private function initId(){
        $column_name = 'id';
        $column = $this->getColumn($column_name);
        $this->setColumn($column_name, $column);

        $control = new DataControl($column_name);
        $control->setType(DataControl::Readonly);
        $this->setEditControl($column_name, $control);
    }
    /**
     * Initialises created_at
     */
    private function initCreatedAt(){
        $column_name = 'created_at';
        $column = $this->getColumn($column_name);
        $column->setVisible(false);
        $this->setColumn($column_name, $column);
    }
    /**
     * Initialises updated_at
     */
    private function initUpdatedAt(){
        $column_name = 'updated_at';
        $column = $this->getColumn($column_name);
        $column->setVisible(false);
        $this->setColumn($column_name, $column);
    }
    /**
     * Initialises name
     */
    private function initName() {
        $column_name = 'name';
        $column = $this->getColumn($column_name);
        $column->setTitle('Name');
        $column->setVisible(true);
        $this->setColumn($column_name, $column);

        $control = $this->getEditControl($column_name);
        $control->setLabel('Name');
        $control->setRequired([
            'message' => 'Please enter a name for this role.',
            'edit' => true,
            'add' => true
        ]);
        $this->setEditControl($column_name, $control);
        $this->setAddControl($column_name, $control);
    }
}
