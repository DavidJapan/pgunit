<?php
/**
 * This extends EditableDataTable to handle the reports table.
 */

namespace App\Models;

use App\AppClasses\DataControl;
use App\AppClasses\SelectBoxControl;
use App\AppClasses\LookupTables\ReportHeadings as LookupReportHeadings;
use App\AppClasses\LookupTables\Officers as LookupOfficers;
use App\AppClasses\LookupTables\CurrentEmployers as LookupEmployers;
use Illuminate\Http\Request;
use DB;
use App\AppClasses\DataTableModelException;
use App\AppClasses\DbMetadata;
use DateTime;
use Auth;
use App\Events\BroadcastingModelEvent;

/**
 * The Report model is quite a complex model because it needs several lookup tables and a toggle control
 * for convenient editing. It uses two database functions to make the complicated set of related tables
 * more convenient for use here. Lookup tables change dynamically as the user edits the report
 * so the client side only requests large data sets from lookup tables when they are required.
 *
 * @author David Mann
 */
class Report extends EditableDataTable {

    /**
     * Set to true because this model needs to automatically store timestamps
     * @var bool 
     */
    public $timestamps = true;
    /**
     * report_id
     * @var string
     */
    protected $primaryKey = 'report_id';
    /**
     * report_heading
     * @var string
     */
    public $name_field = 'report_heading';
    /**
     * The path to the special view we're using for this model: reports/reports
     * @var string
     */
    protected $view_path = 'reports/reports';
    /**
     * The postgreSQL function reports_all_with_headings_get
     * @var string 
     */
    public static $all_function = 'reports_all_with_headings_get';
    /**
     * The postgreSQL function reports_one_with_headings_get
     * @var string 
     */
    public static $one_function = 'reports_one_with_headings_get';
    /**
     * [[1, 'desc']]
     * @var array (2-dimensional array
     */
    public $order = [[1, 'desc']];
    /**
     * You need to add view_model_reports.js last 
     * @var type 
     */
    private $report_scripts = [
        '/js/crud/reports.js',
        '/js/crud/knockout-datetimepicker.js',
        '/js/crud/knockout-datetimepicker-standalone.js',
        '/js/crud/view_model_reports.js'
    ];
    /**
     * Initialises the columns
     * @param array $attributes
     */
    public function __construct(array $attributes = array()) {
        parent::__construct($attributes);
        //$this->hideColumns();
        $this->scripts = $this->mergeReportScripts($this->scripts);
        $this->initUpdatedAt();
        $this->initReportHeadingId();
        $this->initReportHeading();
        $this->initReportId();
        $this->initDescription();
        $this->initEventDate();
        $this->initEmployers();
        $this->initOfficers();
        $this->initCreatedById();
        $this->initCreatedBy();
        $this->initCreatedAt();
        $this->initModifiedById();
        $this->initModifiedBy();
        $this->initInclude();
        $this->initRelatedOrganisation();
    }

    /**
     * Returns a collection of employers gathered using the postgreSQL function employers_available_for_report_get
     * @param type $report_id
     * @return type
     */
    public function getAvailableEmployers($report_id) {
        $select = 'SELECT * FROM employers_available_for_report_get(?)';
        $raw_data = DB::select($select, [$report_id]);
        return collect($raw_data);
    }

    /**
     * Returns a collection of officers gathered using the postgreSQL function officers_available_for_report_get
     * @param type $report_id
     * @return type
     */
    public function getAvailableOfficers($report_id) {
        $select = 'SELECT * FROM officers_available_for_report_get(?)';
        $raw_data = DB::select($select, [$report_id]);
        return collect($raw_data);
    }
    /**
     * Updates the employers list for this report using the postgreSQL function reports_employers_update
     * @param array $employers
     * @param string $id
     * @return array
     * @throws DataTableModelException
     */
    public function updateEmployers($employers, $id) {
        $counter = 0;
        $function_name = 'reports_employers_update';
        if (DbMetadata::functionExists(config('datatables.SCHEMA'), $function_name)) {
            $sql = 'SELECT * FROM ' . $function_name . '(' . $id . ', array[';
            if (!is_null($employers)) {
                if (count($employers) > 0) {
                    foreach ($employers as $employer) {
                        if ($counter != 0) {
                            $sql .= ', ';
                        }
                        $sql .= $employer['employer_id'];
                        $counter += 1;
                    }
                }
            }
            $sql .= ']::integer[])';
            $update_employers_result = DB::select($sql);
            return $update_employers_result[0];
            //$json->data->update_employers_result = $update_employers_result[0];
        } else {
            throw new DataTableModelException('The function \'' . $function_name . '\' doesn\'t exist.');
        }
    }
    /**
     * Updates the officers list for this report using the postgreSQL function reports_officers_update
     * @param array $officers
     * @param string $id
     * @return object
     * @throws DataTableModelException
     */
    public function updateOfficers($officers, $id) {
        $function_name = 'reports_officers_update';
        $counter = 0;
        if (DbMetadata::functionExists(config('datatables.SCHEMA'), $function_name)) {
            $sql = 'SELECT * FROM ' . $function_name . '(' . $id . ', array[';
            if (!is_null($officers)) {
                if (count($officers) > 0) {
                    foreach ($officers as $officer) {
                        if ($counter != 0) {
                            $sql .= ', ';
                        }
                        $sql .= $officer['officer_id'];
                        $counter += 1;
                    }
                }
            }
            $sql .= ']::integer[])';
            //return $sql;
            $update_officers_result = DB::select($sql);
            return $update_officers_result[0];
        } else {
            throw new DataTableModelException('The function \'' . $function_name . '\' doesn\'t exist.');
        }
    }
    /**
     * Processed a suitable object which can be sent to the client as a JSON string.
     * @param Request $request
     * @param type $directory
     * @param type $id
     * @return type
     * @throws DataTableModelException
     */
    public function handleSuccessfulUpdate(Request $request, $directory, $id) {
        try {
            $employers = $request->input("employers");
            $officers = $request->input("officers");
            $json = new \stdClass();
            $json->data = new \stdClass();
            //if (!is_null($employers)) {
            $json->data->update_employers_result = $this->updateEmployers($employers, $id);
            //}
            //if (!is_null($officers)) {
            $json->data->update_officers_result = $this->updateOfficers($officers, $id);
            //}
            $directory = $this->directory;
            $model = new Report();
            //We use the instance method here because we want to call the one_function if it exists.
            $raw = $model->one($request, [$id]);
            $processed = $raw;
            $processed->employers = json_decode($raw->employers);
            $processed->officers = json_decode($raw->officers);
            $json->data->$directory = $processed;
            return json_encode($json);
        } catch (\Throwable $t) {
            throw new DataTableModelException($t->getMessage(), $t->getCode());
        }
    }
    /**
     * Adds the current user to the view data sent to the client. The client-side form
     * needs to be able to send details of the current user when adding or editing a report.
     * @return type
     */
    public function getViewData() {
        $current_user = new \stdClass();
        $current_user->id = Auth::user()->id;
        $current_user->givenname = Auth::user()->givenname;
        $current_user->familyname = Auth::user()->familyname;
        $this->view_data['current_user'] = $current_user;
        return $this->view_data;
    }
    /**
     * Checks that the specified data is in the correct format
     * @param type $date
     * @param type $format
     * @return bool
     */
    public static function validateDate($date, $format = 'M-d-Y') {
        $d = DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date;
    }
    /**
     * The reports view allows some quite complex filtering. This assembles
     * a suitable select query to respond to the user's filtering settings.
     * @param Request $request
     * @return string
     */
    private static function getSelectFilteredByColumns(Request $request) {
        $select = "";
        $report_heading_id = $request->input("report_heading_id");
        $employers_string = $request->input("employers_string");
        $description = $request->input("description");
        $event_date_from = $request->input("event_date_from");
        $event_date_to = $request->input("event_date_to");
        $search_values = new \stdClass();
        if ($report_heading_id != "" && $report_heading_id != 0) {
            $search_values->report_heading_id = $report_heading_id;
        }
        if ($employers_string != "") {
            $search_values->employers_string = $employers_string;
        }
        if ($description != "") {
            $search_values->description = $description;
        }
        if ($event_date_from != "") {
            $search_values->event_date_from = $event_date_from;
        }
        if ($event_date_to != "") {
            $search_values->event_date_to = $event_date_to;
        }
        $index = 0;
        foreach ($search_values as $col_name => $value) {
            if ($index == 0) {
                $select .= " WHERE ";
                $index++;
            } else {
                $select .= " AND ";
            }
            if (static::validateDate($value)) {
                if ($col_name == 'event_date_from') {
                    $select .= "event_date " . " >= '" . $value . "'";
                } else {
                    $select .= "event_date " . " <= '" . $value . "'";
                }
            } else if (is_numeric($value)) {
                $select .= $col_name . " = " . $value;
            } else {
                $select .= $col_name . " ILIKE '%" . $value . "%'";
            }
        }
        return $select;
    }
    /**
     * The reports view allows some quite complex filtering. This filters according
     * to the user's settings and returns the number of rows found.
     * @param Request $request
     * @return type
     */
    public static function getRecordsFilteredByColumnsCount(Request $request) {
        $select = 'select count(*) FROM ' . static::getAllFunction() . '()';
        $select .= static::getSelectFilteredByColumns($request);
        $recordsFiltered = DB::select($select)[0]->count;
        return (int) $recordsFiltered;
    }
    /**
     * The reports view allows some quite complex filtering. This filters according
     * to the user's settings and returns the actual data found filterd by columns.
     * @param Request $request
     * @param type $limit
     * @param type $offset
     * @param type $order_by
     * @param type $search
     * @return type
     */
    public static function getDataFilteredByColumns(Request $request, $limit = null, $offset = null, $order_by = null, $search) {
        $select = 'select * FROM ' . static::getAllFunction() . '() ';
        $select .= static::getSelectFilteredByColumns($request);
        if (!is_null($order_by)) {
            $select .= " ORDER BY " . $order_by;
        }
        if (!is_null($limit)) {
            $select .= " LIMIT " . $limit . " ";
        }
        if (!is_null($limit)) {
            $select .= " OFFSET " . $offset;
        }
        $select .= ";";
        //$raw_results = DB::select($select, []);
        $raw_results = collect(DB::select($select, []));
        $map = $raw_results->map(function($item, $key) {
            $item->employers = json_decode($item->employers);
            $item->officers = json_decode($item->officers);
            return $item;
        });
        return $map->all();
    }
    /**
     * Initialises the report_id column
     */
    protected function initReportId() {
        $column_name = 'report_id';
        $column = $this->getColumn($column_name);
        $column->setTitle('ID');
        $column->setVisible(true);
        $column->setWidth('5%');
        $this->setColumn($column_name, $column);
        $control = new DataControl($column_name);
        $control->setType(DataControl::Readonly);
        $this->setEditControl($column_name, $control);
    }
    /**
     * Merges the special scripts needed for this model with the standard ones needed
     * by EditableDataTable
     * @param type $scripts
     * @return type
     */
    public function mergeReportScripts($scripts) {
        return array_merge($scripts, $this->report_scripts);
    }

    /**
     * This deletes the report with the specified ID. This function calls
     * the PostgreSQL function 'delete_report'. If a function is called and not found,
     * an error is triggered, so this function checks if the function exists first before
     * running. If the function doesn't exist, a DataTableModelException is thrown.
     * @param string $id
     * @return int
     * @throws DataTableModelException
     */
    public function deleteReport($id) {
        $function_name = 'reports_one_delete';
        $deleted_model = $this->one([$id]);
        $deleted_model->employers = json_decode($deleted_model->employers);
        $deleted_model->officers = json_decode($deleted_model->officers);
        //$deleted_model_array = $deleted_model->toArray();
        /*
        $obj = new \stdClass();
        $obj->reports_one_delete = $deleted_model;
        $response = [
            $obj
        ];
        return $response;
         * 
         */
        if (DbMetadata::functionExists(config('datatables.SCHEMA'), $function_name)) {
            $sql = 'SELECT * FROM ' . $function_name . '(' . $id . ')';
            $delete_report_result = DB::select($sql);
            event(new BroadcastingModelEvent($deleted_model, 'deleted'));
            return $delete_report_result;
        } else {
            throw new DataTableModelException('The function \'' . $function_name . '\' doesn\'t exist.');
        }
    }
    /**
     * Initialises the updated_at column.
     */
    protected function initUpdatedAt() {
        $column_name = 'updated_at';
        $column = $this->getColumn($column_name);
        $column->setTitle('Last Modified');
        $column->setMoment(true);
        $column->setVisible(true);
        $this->setColumn($column_name, $column);

        $control = new DataControl($column_name);
        $control->setType(DataControl::Readonly);
        $this->setAddControl($column_name, $control);
        $this->setEditControl($column_name, $control);
    }
    /**
     * Initialises the created_at column.
     */
    protected function initCreatedAt() {
        $column_name = 'created_at';
        $column = $this->getColumn($column_name);
        $column->setTitle('Created');
        $column->setVisible(true);
        $column->setMoment(true);
        $this->setColumn($column_name, $column);
        $control = new DataControl($column_name);
        $this->setAddControl($column_name, $control);
        $this->setEditControl($column_name, $control);
    }
    /**
     * Initialises the event_date column.
     */
    protected function initEventDate() {
        $column_name = 'event_date';
        $column = $this->getColumn($column_name);
        $column->setTitle('Event Date');
        $column->setMoment(true);
        $this->setColumn($column_name, $column);
        $control = new DataControl($column_name);
        $control->setMoment(true);
        $control->setType(DataControl::DatePicker);
        $this->setAddControl($column_name, $control);
        $this->setEditControl($column_name, $control);
    }
    /**
     * Initialises the report_heading column.
     */
    protected function initReportHeading() {
        $control_name = 'report_heading';
        $column = $this->getColumn($control_name);
        $column->setVisible(true);
        $this->setColumn($control_name, $column);
        $control = new DataControl($control_name);
        $this->setAddControl($control_name, $control);
        $this->setEditControl($control_name, $control);
    }
    /**
     * Initialises the report_heading_id column.
     */
    protected function initReportHeadingId() {
        $control_name = 'report_heading_id';
        $column = $this->getColumn($control_name);
        $column->setVisible(false);

        $control = new SelectBoxControl($control_name);
        $control->setLabel('Report Headings');
        $control->setRequired([
            'message' => 'Please choose a report heading.',
            'edit' => true,
            'add' => true
                ]
        );
        $control->setOptions('report_heading_list');
        $control->setOptionsText('report_heading');
        $control->setOptionsValue('report_heading_id');
        $control->setValue('report_heading_id');
        $control->setOptionsCaption('Choose report heading...');
        $control->setChangeEvent('reportHeadingChanged');
        $this->setEditControl($control_name, $control);
        $this->setAddControl($control_name, $control);
    }
    /**
     * Initialises the created_by_id
     */
    protected function initCreatedById() {
        $control_name = 'created_by_id';
        $column = $this->getColumn($control_name);
        $column->setVisible(false);
        $control = new DataControl($control_name);
        $this->setEditControl($control_name, $control);
    }
    /**
     * Initialises the created_by column.
     */
    protected function initCreatedBy() {
        $control_name = 'created_by';
        $column = $this->getColumn($control_name);
        $column->setVisible(false);
        $control = new DataControl($control_name);
        $this->setEditControl($control_name, $control);
    }
    /**
     * Initialises the modified_by_id column.
     */
    protected function initModifiedById() {
        $control_name = 'modified_by_id';
        $column = $this->getColumn($control_name);
        $column->setVisible(false);
        $control = new DataControl($control_name);
        $this->setEditControl($control_name, $control);
    }
    /**
     * Initialises the modified_by column.
     */
    protected function initModifiedBy() {
        $control_name = 'modified_by';
        $column = $this->getColumn($control_name);
        $column->setVisible(false);
        $control = new DataControl($control_name);
        $this->setEditControl($control_name, $control);
    }
    /**
     * Initialises the related_organisation column.
     */
    private function initRelatedOrganisation() {
        $control_name = 'related_organisation';
        $column = $this->getColumn($control_name);
        $column->setVisible(true);
        $control = new DataControl($control_name);
        $this->setAddControl($control_name, $control);
        $this->setEditControl($control_name, $control);
    }
    /**
     * Initialises the include column.
     */
    private function initInclude() {
        $control_name = 'include';
        $column = $this->getColumn($control_name);
        $column->setVisible(false);
    }
    /**
     * Initialises the description column.
     */
    protected function initDescription() {
        $control_name = 'description';
        $column = $this->getColumn($control_name);
        $column->setTemplateId('cropped_description');
        $column->setWidth('25%');
    }
    /**
     * Initialises the employers and employers_string columns.
     */
    protected function initEmployers() {
        $control_names = ['employers_string', 'employers'];
        foreach ($control_names as $control_name) {
            $column = $this->getColumn($control_name);
            $column->setVisible(false);
            $control = new DataControl($control_name);
            $control->setArray(true);
            if ($control_name == 'employers_string') {
                $control->setArray(false);
                $column->setTitle('Employers');
                $column->setVisible(true);
            } else {
                $column->setIsJson(true);
                $column->setIsArray(true);
            }
            if ($control_name != 'employers_string') {
                $control->setRequired([
                    'message' => 'Please choose an employer.',
                    'edit' => true,
                    'add' => true
                        ]
                );
                $this->setEditControl($control_name, $control);
                $this->setAddControl($control_name, $control);
            }
        }
    }
    /**
     * Initialises the officers and officers_string columns.
     */
    protected function initOfficers() {
        $control_names = ['officers_string', 'officers'];
        foreach ($control_names as $control_name) {
            $column = $this->getColumn($control_name);
            if ($control_name == 'officers') {
                $column->setVisible(false);
            }
            $control = new DataControl($control_name);
            $control->setArray(true);
            if ($control_name == 'officers_string') {
                $control->setArray(false);
                $column->setTitle('Officers');
            } else {
                $column->setIsJson(true);
                $column->setIsArray(true);
            }
            if ($control_name != 'officers_string') {
                $this->setEditControl($control_name, $control);
                $this->setAddControl($control_name, $control);
            }
        }
    }
    /**
     * Gets the quite complicated set of lookup tables for handling report headings,
     * officers and employers.
     * @param type $bindings
     * @return type
     */
    public function getLookupTables($bindings = []) {
        $lookupReportHeadings = new LookupReportHeadings();
        $lookupEmployers = new LookupEmployers();
        $lookupOfficers = new LookupOfficers();
        $report_heading_list = $lookupReportHeadings->get();
        $officer_list = $lookupOfficers->get();
        $employer_list = $lookupEmployers->get();
        $lookup_tables = [
            'report_heading_list' => $report_heading_list,
            'officer_list' => $officer_list,
            'employer_list' => $employer_list
        ];
        return $lookup_tables;
    }
    /**
     * May be obsolete. I thought I had to include employers and officers here
     * but actually the client side requests those lookup tables as required.
     * @param Request $request
     * @param type $bindings
     * @return type
     */
    public function one($bindings = []) {
        $one = parent::one($bindings);
        //$one->employers = json_decode($one->employers);
        //$one->officers = json_decode($one->officers);
        return $one;
    }
    /**
     * Need a convenient function to serialize the model including employers and officers
     * @return array
     */
    public function toArray(){        
        $one = $this->one([$this->report_id]);
        $one->employers = json_decode($one->employers);
        $one->officers = json_decode($one->officers);
        return $one;
    }
    /**
     * Required by the SSP trait. Gets the actual data as a collection filtered according
     * to the user's choices.
     * @param int $limit
     * @param int $offset
     * @param string $order_by
     * @param string $search
     * @param array $searchable_columns
     * @return collection
     */ 
    public static function getFilteredData($limit = null, $offset = null, $order_by = "id asc", $search = null, $searchable_columns = null) {
        $select = 'select * FROM ' . static::getAllFunction() . '()';
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
        $select .= " ORDER BY " . $order_by;
        if (!is_null($limit)) {
            $select .= " LIMIT " . $limit . " ";
        }
        if (!is_null($limit)) {
            $select .= " OFFSET " . $offset;
        }
        $select .= ";";
        $raw_results = collect(DB::select($select, []));
        $map = $raw_results->map(function($item, $key) {
            $item->employers = json_decode($item->employers);
            $item->officers = json_decode($item->officers);
            return $item;
        });
        return $map->all();

        return $map;
    }
    /**
     * This if for use after adding a new report. Uses the postgreSQL function reports_new_with_headings_get
     * @param Request $request
     * @param type $bindings
     * @return type
     * @throws DataTableModelException
     */
    private function one_from_new_function(Request $request, $bindings = []) {
        $function = 'reports_new_with_headings_get';
        try {
            if (DbMetadata::functionExists(config('datatables.SCHEMA'), $function)) {
                $select = self::getSelectFromBindings(['*'], $bindings, $function);
                $one = DB::select($select, $bindings);
                return collect($one)->first();
            } else {
                throw new DataTableModelException('The function ' . $function . ' doesn\'t exist.');
            }
        } catch (\Exception $e) {
            throw new DataTableModelException("There was a problem trying find one row of this table: " . $e->getMessage());
        }
    }
    /**
     * Calls the one_from_new_function.
     * @param Request $request
     * @param type $bindings
     * @return type
     */
    public function new_one(Request $request, $bindings = []) {
        $one = $this->one_from_new_function($request, $bindings);
        return $one;
    }

}
