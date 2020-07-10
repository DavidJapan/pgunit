<?php
/**
 * Adds the necessary functions to handle server-side processing for a jQuery Data Table
 */
namespace App\Traits;

use Illuminate\Http\Request;
use DB;

/**
 * Description of SSP: This provides the necessary response for a jQuery DataTable which expects to 
 * be processed on the server side.
 *
 * @author David Mann
 */
trait SSP {

    /**
     * This provides the necessary response for a jQuery DataTable which expects to 
     * be processed on the server side.
     * @param Request $request
     * @return type
     */
    public function ssp(Request $request) {
        $this->init($request);
        //Use the naming convention from datatables here
        $response = new \stdClass();
        $start = (int) $request->input("start");
        $length = (int) $request->input("length");
        $order = $request->input("order");
        $columns = $request->input("columns");
        $searchable_columns = [];
        foreach ($columns as $column) {
            $col_name = $column["data"];

            if ($col_name !== 'selector') {
                if ($column["searchable"]) {
                    array_push($searchable_columns, $col_name);
                }
            }
            $column_search_array = $column["search"];
        }
        $order_column = $columns[(int) $order[0]['column']]['data'];
        //return $order_column;
        $direction = $order[0]['dir'];
        $order_by = $order_column . " " . $direction;
        //return $order_by;
        $search_array = $request->input("search");
        $search = $search_array["value"];
        $response->search = $search;
        $response->searchable_columns = $searchable_columns;
        $response->order_by = $order_by;
        $response->columns = $columns;
        // The draw counter that this object is a response to - from the draw parameter sent as part of the data request. 
        // Note that it is strongly recommended for security reasons that you cast this parameter to an integer, 
        // rather than simply echoing back to the client what it sent in the draw parameter, 
        // in order to prevent Cross Site Scripting (XSS) attacks.
        $response->draw = (int) $request->input("draw");
        try {
            //The getFillable method can throw an Exception.
            $response->fillable = $this->model->getFillable();
            //Total records, before filtering (i.e. the total number of records in the database)
            $response->recordsTotal = $this->getRecordsTotal();
            //Total records, after filtering (i.e. the total number of records after filtering has been applied -
            //not just the number of records being returned for this page of data).
            //The data to be displayed in the table. This is an array of data source objects, one for each row,
            //which will be used by DataTables.
            //Note that this parameter's name can be changed using the ajax option's dataSrc property.
            //Note that getBookProgressRecords uses Postgresql naming conventions. $length is passed to the $limit
            //parameter and $start to the $offset parameter. These are translated into LIMIT and OFFSET statements in the select query.
            $data = new \stdClass();
            $directory = $this->model->directory();
            //if ($search instanceof stdClass) {
            if($search == 'by_columns'){
                //The client has asked to search by specific columns
                $response->recordsFiltered = $this->getRecordsFilteredByColumnsCount($request);
                $data->$directory = $this->getDataFilteredByColumns($request, $length, $start, $order_by, $search);
            } else {
                $response->recordsFiltered = $this->getRecordsFilteredCount($search, $searchable_columns);
                $data->$directory = $this->getFilteredData($length, $start, $order_by, $search, $searchable_columns);
            }
            $response->data = $data;
            return json_encode($response);
        } catch (DataTableModelException $de) {
            return $this->handleException($de);
        }
    }

    /**
     * Gets the total number of records possible.
     * @return int
     */
    private function getRecordsTotal() {
        $select = 'select count(*) FROM ' . $this->model->getTable() . ';';
        $recordsTotal = DB::select($select)[0]->count;
        return (int) $recordsTotal;
    }
    /**
     * Gets the count of record filtered by columns.
     * @param Request $request
     * @return int
     */
    protected function getRecordsFilteredByColumnsCount(Request $request) {
        $model_class = $this->model_class;
        return $model_class::getRecordsFilteredByColumnsCount($request);
    }
    /**
     * Gets the count of records filtered.
     * @param string $search
     * @param array $searchable_columns
     * @return int
     */
    private function getRecordsFilteredCount($search = null, $searchable_columns) {
        $model_class = $this->model_class;
        $select = 'select count(*) FROM ' . $model_class::getAllFunction() . '()';
        if (!empty($search)) {
            for ($i = 0; $i < count($searchable_columns); $i++) {
                $col = "CAST(" . $searchable_columns[$i] . " AS TEXT)";
                if ($i == 0) {
                    $select .= " WHERE " . $col . " ILIKE '%" . $search . "%'";
                } else {
                    $select .= " OR " . $col . " ILIKE '%" . $search . "%'";
                }
            }
        }
        $select .= ";";
        $recordsFiltered = DB::select($select)[0]->count;
        return (int) $recordsFiltered;
    }
    /**
     * Gets a collection of the actual data filtered by columns.
     * @param Request $request
     * @param int $limit
     * @param int $offset
     * @param string $order_by
     * @param string $search
     * @return Collection
     */
    protected function getDataFilteredByColumns(Request $request, $limit = null, $offset = null, $order_by, $search){
        $model_class = $this->model_class; 
        return $model_class::getDataFilteredByColumns($request, $limit, $offset, $order_by, $search);
    }
    /**
     * Gets the actual data filtered according to the user's choices.
     * @param int $limit
     * @param int $offset
     * @param string $order_by
     * @param string $search
     * @param array $searchable_columns
     * @return collection
     */
    private function getFilteredData($limit = null, $offset = null, $order_by = "id asc", $search = null, $searchable_columns = null) {
        $model_class = $this->model_class;
        return $model_class::getFilteredData($limit, $offset, $order_by, $search, $searchable_columns);
    }

}
