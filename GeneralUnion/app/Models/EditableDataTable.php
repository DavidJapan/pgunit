<?php

namespace App\Models;

use App\AppClasses\DataTableColumn;
use Illuminate\Http\Request;
use App\AppClasses\DataTableModelException;

//use PDOException;

/**
 * Description of EditableDataTable
 * 
 * Extends the basic DataTable model by using the AddItemTrait and EditItemTrait to provide all the 
 * CRUD functions required for editing a DataTable.
 * 
 * @uses AddItemTrait
 * @uses EditItemTrait
 * @extends DataTable
 *
 * @author David Mann
 */
class EditableDataTable extends DataTable {

    use AddItemTrait,
        EditItemTrait,
        EditInlineTrait;

    /**
     * The relative path to the view to use to display the table handled by this model.
     * @var string This is the relative path from the parent folder resources/views. 
     */
    protected $view_path = 'tables/single-editable-table';

    /**
     * The constructor calls the parent constructor. The root of this inheritance chain is 
     * Illuminate\Database\Eloquent\Model whose constructor requires the attributes array
     * This constructor initialises the add and edit controls to be used in dialog boxes on 
     * the client side for manipulating the table which this model is responsible for. It also adds
     * any extra JavaScript scripts required for this particular model.
     * @param array $attributes
     */
    public function __construct(array $attributes = array()) {
        parent::__construct($attributes);
        $this->initialiseAddControls();
        $this->scripts = $this->mergeAddScripts($this->scripts);
        $this->initialiseEditControls();
        $this->scripts = $this->mergeEditScripts($this->scripts);
    }

    public function initViewData() {
        parent::initViewData();
        //$this->view_data['add_item_form_id'] = $this->addItemFormId();
        //$this->view_data['item_editor_form_id'] = $this->itemEditorFormId();
        $initEditInlineViewData = $this->initEditInlineViewData();
        $this->view_data = array_merge($this->view_data, $initEditInlineViewData);
    }

    /**
     * Gets the hash of data from 'lookup' tables - typically tables need
     * to populate drop-down boxes in dialog boxes.
     * @param type $bindings
     * @return array
     */
    public function getLookupTables($bindings = []) {
        return [];
    }

    /**
     * We pass the object by reference and return it modified.
     * @param \stdClass $object
     * @return \stdClass
     */
    public function addLookupTables($object, $bindings = []) {
        $object->lookup_tables = new \stdClass();
        $lookup_tables = $this->getLookupTables($bindings);
        if (count($lookup_tables) > 0) {
            foreach ($lookup_tables as $lookup_table => $value) {
                $object->lookup_tables->$lookup_table = $value;
            }
        }
    }

    protected function initialiseColumns() {
        $fields = $this->getAllFields();
        $this->columns = [];
        $index = 0;
        $column = new DataTableColumn();
        //$column->setIsPrimaryKey($field == $this->getKeyName());
        $column->setData('selector');
        $column->setTitle('<i class="fa fa-square"></i>');
        $column->setTemplateId('selector');
        $column->setOrderable(false);
        $column->setTargets($index);
        $this->columns['selector'] = $column;
        $index += 1;
        foreach ($fields as $field) {
            $column = new DataTableColumn();
            //$column->setIsPrimaryKey($field == $this->getKeyName());
            $column->setData($field);
            $column->setTitle($this->camelCaseToCapitalised($field));
            $column->setTargets($index);
            $this->columns[$field] = $column;
            $index += 1;
        }
    }

    public function deleteModel(Request $request) {
        //try {
            //$this->init($request);
            //$model_class = $this->model_class;
            $primaryKey = $this->getKeyName();
            $id = $request->$primaryKey;
            $model_instance = static::find($id);
            $result = $model_instance->delete();
            return $result;
            //I've had to add Query Exception to the catch block because that's the one which
            //catches the error codes from Postgresql that indicate things like a violation of
            //a foreign key constraint.
            /*
        } catch (\QueryException $qe) {
            throw new DataTableModelException($qe);
        } catch (\PDOException $t) {
            throw new DataTableModelException($t);
        } catch (\Throwable $t) {
            throw new DataTableModelException($t);
        }
             * 
             */
    }

}
