<?php
/**
 * This controller extends DataTableController to handle editable data tables.
 */

namespace App\Http\Controllers;

use App\Traits\AddItem;
use App\Traits\EditItem;
use App\Traits\EditInline;
use Illuminate\Http\Request;
use App\AppClasses\DataTableModelException;


/**
 * EditableDataTableController extends DataTableController to provide the functionality needed
 * to edit a jQuery DataTable. It uses the AddItem and EditItem traits.  There are some situations while editing a table where it is useful to be able look up a hash of all 
 * the values in a given table, so it adds one method - getAllHash.
 * 
 * @see AddItem
 * 
 * @see EditItem
 * 
 * @extends DataTableController
 * @uses initEditViewData
 * 
 * @author David Mann
 */
class EditableDataTableController extends DataTableController {

    use AddItem,
        EditItem;
    /**
     * Adds view data from the inline Edit trait, calling the initEditInlineViewData method
     * from the model.
     */
    protected function initEditInlineViewData() {
        $edit_inline_view_data = $this->model->initEditInlineViewData();
        $this->view_data = array_merge($this->view_data, $edit_inline_view_data);
    }
    /**
     * In addition to the initialistion done by the parent class, this initialised view data necessary 
     * for editing a data table and adds lookup tables.
     * @param Request $request
     * @param type $directory
     * @throws DataTableModelException
     */
    public function init(Request $request = null, $directory = null) {
        parent::init($request, $directory);
        try {
            $this->initAddViewData();
            $this->initEditViewData();
            $this->initEditInlineViewData();
            $lookup_tables = $this->model->getLookupTables();
            $this->view_data = array_merge($this->view_data, ['lookup_tables' => $lookup_tables]);
        } catch (\Throwable $t) {
            throw new DataTableModelException($t->getMessage(), $t->getCode());
        }
    }

    /**
     * This calls the getAllHash method from the associated model. The key parameter is
     * crucial because we have to specify which field from the database table to use as the key for
     * the hash map. The developer is responsible for making sure this field has unique values. This
     * method doesn't guarantee that.
     * 
     * @param Request $request
     * 
     * @param string $key
     * 
     * @return string JSON-encoded string of all values in the database table handled by the model associated with this controller.
     */
    public function getAllHash(Request $request, $key) {
        return json_encode($this->model->getAllHash(['*'], [], $key, []));
    }

}
