<?php
/**
 * This trait gives a EditableDataTableController the ability to edit an item in a data table.
 */
namespace App\Traits;

use Illuminate\Http\Request;
use App\AppClasses\DataTableModelException;
use DB;

/**
 * This trait gives a EditableDataTableController the ability to edit an item in a data table.
 *
 * @author David Mann
 */
trait EditItem {

    /**
     * Initialises the $edit_view_data hash and merges it with $view_data
     */
    protected function initEditViewData() {
        $edit_view_data = $this->model->initEditViewData();
        $this->view_data = array_merge($this->view_data, $edit_view_data);
    }

    /**
     * Takes values from the client-side AJAX PUT request, finds the relevant
     * record in the database and returns the updated model or passes 
     * a relevant error message.
     * @uses handleSuccessfulUpdate
     * @param Request $request
     * @return string JSON-encoded string representing
     * the row just updated.
     */
    public function update(Request $request) {
        $this->init($request);
        try {
            $model = $this->model;
            return $model->updateModel($request, $this->model->directory());
        } catch (\Throwable $t) {
            throw new DataTableModelException($t->getMessage(), $t->getCode());
        }
    }

    /**
     * Returns a JSON-encoded string with the boolean property result for success or failure.
     * @param Request $request
     * @param bool $result
     * @return string JSON-encoded string with the property result.
     */
    protected function handleSuccessfulDelete(Request $request, $result) {
        $json = new \stdClass();
        $json->result = $result;
        return json_encode($json);
    }

    /**
     * Destroys the specified model and sends a result to the client-side or handles any
     * exception.
     * @see DataTableController::handleException
     * @uses handleSuccessfulDelete
     * @param Request $request
     * @return bool
     */
    public function delete(Request $request) {
        try {
            $this->init($request);
            $model_class = $this->model_class;
            $primaryKey = $this->primaryKey;
            $id = $request->$primaryKey;
            //Don't destroy it here. Use the delete method from the model.
            //$result = $model_class::destroy($id); 
            $result = $this->model->deleteModel($request);
            return $this->handleSuccessfulDelete($request, $result);
        } catch (\Throwable $t) {
            return $this->handleException($t);
        }
    }

}
