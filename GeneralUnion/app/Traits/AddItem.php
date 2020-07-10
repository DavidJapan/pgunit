<?php
/**
 * This trait provides a DataTable with the ability to add an item to its data set.
 */
namespace App\Traits;

use App\AppClasses\DataTableModelException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use DB;

/**
 * This trait provides a DataTable with the ability to add an item to its data set.
 *
 * @author David Mann
 */
trait AddItem {

    /**
     *
     * @var array A hash with the following properties:
     * <ul>
     * <li>
     * item: the name to display for an item from this data table
     * </li>
     * <li>
     * add_dialog_width: the width to display the add item dialog box. 
     * Can be modal-lg or modal-sm or (the default) zero-length string
     * </li>
     * <li>
     * add_controls: an array of controls needed when adding a new item
     * </li>
     * <li>
     * can_add: a boolean value. The default value of true means that an item can be added to this data table.
     * </li>
     * </ul>
     * @uses AddItemTrait::getAddDialogWidth
     */
    protected $add_view_data;

    /**
     * Initiates the add_view_data property and merges with the view_data property
     * of the class using this trait.
     * 
     * @see add_view_data
     * $return void
     */
    protected function initAddViewData() {
        $this->add_view_data = [
            'add_dialog_width' => $this->model->getAddDialogWidth(),
            'scripts' => $this->model->scripts(),
            'add_controls' => $this->model->addControls()
        ];
        $this->view_data = array_merge($this->view_data, $this->add_view_data);
    }

    /**
     * Populates an instance of the current model with data from the request
     * then saves the instance to the database. If successful, it calls the handleSuccessfulStore
     * method.
     * @throws PDOException, QueryException, Exception
     * @uses handleSuccessfulStore
     * @param Request $request
     * @return string
     */
    public function store(Request $request) {
        try {
            $this->init($request);
            $model = $this->model;
            $primaryKey = $this->primaryKey;
            $fields = $model->getFillable();
            for ($i = 0; $i < count($fields); $i++) {
                $field_name = $fields[$i];
                if (!is_null($request->input($field_name))) {
                    $model[$field_name] = $request->input($field_name);
                }
            }
            //This is to accommodate undo posts where we are adding a pre-existing record.
            if (!is_null($request->input($primaryKey))) {
                $primaryKeyValue = $request->input($primaryKey);
                $model->$primaryKey = $primaryKeyValue;
            }
            $result = $model->save();
            if ($result) {
                
                return $this->handleSuccessfulStore($request, $model->$primaryKey);
            } else {
                $error = $model->errors()->all(':message');
                $json = new \stdClass();
                $json->error = $error;
                return json_encode($json);
            }
            return json_encode($model);
        } catch (\Throwable $t) {
            throw new DataTableModelException($t->getMessage(), $t->getCode());
        }
    }

    /**
     * Creates an object with the property data. Adds the current URL as a property of the data object.
     * Retrieves the new row just added to the data table and assigns it to URL property of data.
     * It then returns this as a JSON-encoded string.
     * The calling page needs to retrieve the data for this row with JavaScript like this:
     * <code>
     * data = json.data[self.tableModel.directory]
     * </code>
     * @throws PDOException, QueryException, Exception
     * @param Request $request
     * @param type $id
     * @return string A JSON-encoded string representing the requested item from a data table.
     */
    public function handleSuccessfulStore(Request $request, $id) {
        try {
            $json = new \stdClass();
            $json->data = new \stdClass();
            $directory = $this->model->directory();
            //We use the instance method here because we want to call the one_function if it exists.
            $json->data->$directory = $this->model->one($request, [$id]);
            return json_encode($json);
        } catch (\Throwable $t) {
            throw new DataTableModelException($t->getMessage(), $t->getCode());
        }
    }

}
