<?php
/**
 * This trait gives a DataTable the ability to have a one-to-many relationship
 * with a select box or another data table of possible parent values.
 */

namespace App\Traits;

use Illuminate\Http\Request;
use App\Models\DataTable;
use App\AppClasses\DataTableModelException;
use DB;

/**
 * This trait gives a DataTable the ability to have a one-to-many relationship
 * with a select box or another data table of possible parent values.
 * 
 * @author David Mann
 */
trait ParentTrait {

    /**
     * Add a child to this parent.
     * @param Request $request
     * @return type
     */
    public function assign(Request $request) {
        $this->init($request);
        return $this->model->assign($request);
    }
    /**
     * Remove a child from this parent.
     * @param Request $request
     * @return type
     */
    public function remove(Request $request) {
        $this->init($request);
        return $this->model->remove($request);
    }

    /**
     * Returns all the children belonging to specified parent.
     * @param Request $request
     * @param string $selected_parent_id
     * @return string A JSON-encoded string of an array of children that belong to the specified parent.
     */
    public function getChildren(Request $request, $selected_parent_id) {
        $this->init($request);
        return $this->model->getChildren($request, $selected_parent_id);
    }
    /**
     * Returns the child just added to the parent and a list of children not yet belonging to the parent.
     * @param Request $request
     * @param string $id
     * @param string $parent_id
     * @return string A JSON-encoded string representing the child just added to a parent
     * and the lists of unused children not yet connected to this parent.
     */
    public function handleSuccessfulStoreToParent(Request $request, $id, $parent_id) {
        try {
            $json = new \stdClass();
            $json->data = new \stdClass();
            $directory = $this->directory;
            //We use the instance method here because we want to call the one_function if it exists.
            $json->data->$directory = $this->model->one($request, [$id]);
            $this->model->addUnused($json, [$parent_id]);
            return json_encode($json);
        } catch (DataTableModelException $dtme) {
            return $this->handleException($dtme);
        } catch (\PDOException $pdo) {
            return $this->handleException($pdo);
        } catch (\QueryException $qe) {
            return $this->handleException($qe);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Adds a child to this parent.
     * @param Request $request
     * @return type
     */
    public function store(Request $request) {
        try {
            $this->init($request);
            $model = $this->model;
            $primaryKey = $this->primaryKey;
            $parent_primary_key = $this->model->parentPrimaryKey();
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
                return $this->handleSuccessfulStoreToParent($request, $model->$primaryKey, $model->$parent_primary_key);
            } else {
                $error = $model->errors()->all(':message');
                $json = new stdClass();
                $json->error = $error;
                return json_encode($json);
            }
        } catch (\PDOException $pdo) {
            return $this->handleException($pdo);
        } catch (\QueryException $qe) {
            return $this->handleException($qe);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}
