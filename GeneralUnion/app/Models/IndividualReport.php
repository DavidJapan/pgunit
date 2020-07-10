<?php

/**
 * This extends EditableDataTable to handle the reports table for a specific user.
 */

namespace App\Models;

use App\AppClasses\DataControl;
use App\AppClasses\SelectBoxControl;
use App\AppClasses\DataTableColumn;
use Illuminate\Http\Request;
use App\AppClasses\DataTableModelException;
use Auth;
use App\AppClasses\LookupTables\ReportHeadings as LookupReportHeadings;
use App\AppClasses\LookupTables\Officers as LookupOfficers;
use App\AppClasses\LookupTables\CurrentEmployers as LookupEmployers;
use DB;
use App\AppClasses\DbMetadata;

/**
 * The IndividualReport model is quite a complex model because it needs several lookup tables and a toggle control
 * for convenient editing. It uses two database functions to make the complicated set of related tables
 * more convenient for use here. Lookup tables change dynamically as the user edits the report
 * so the client side only requests large data sets from lookup tables when they are required. It also
 * needs to limit the data it provides to what is relevant to the current user so it calls the static 
 * method user() from the Auth class to get the current user's ID.
 *
 * @author David Mann
 */
class IndividualReport extends EditableDataTable {

    /**
     * Set to true
     * @var bool
     */
    public $timestamps = true;

    /**
     * reports
     * @var string
     */
    protected $table = 'reports';

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
     * Report
     * @var string
     */
    public $item_name = 'Report';

    /**
     * The relative path to the view we want to use to display this table.
     * reports/individual_reports
     * @var string
     */
    protected $view_path = 'reports/individual_reports';

    /**
     * reports_all_with_headings_for_user_get
     * @var string
     */
    public static $all_function = 'reports_all_with_headings_for_user_get';

    /**
     * reports_one_with_headings_for_user_get
     * @var string 
     */
    public static $one_function = 'reports_one_with_headings_for_user_get';

    /**
     * The extra scripts needed for the view
     * @var array 
     */
    private $individual_report_scripts = [
        '/js/crud/individual_reports.js',
        '/js/crud/knockout-datetimepicker.js',
        '/js/crud/view_model_individual_reports.js'
    ];

    /**
     * Initialises the columns.
     * @param array $attributes
     */
    public function __construct(array $attributes = array()) {
        parent::__construct($attributes);
        $this->scripts = array_merge($this->scripts, $this->individual_report_scripts);
        $this->initEmployers();
        $this->initOfficers();
        $this->initEventDate();
        $this->initReportHeading();
        $this->initReportHeadingId();
        $this->initCreatedBy();
        $this->initCreatedById();
        $this->initModifiedById();
        $this->initModifiedBy();
        $this->initInclude();
        $this->initCreatedAt();
        $this->initUpdatedAt();
        $this->initDescription();
    }

    /**
     * Adds data about the current user to the normal view data for an editable data table.
     * @return array
     */
    public function getViewData() {
        $current_user = new \stdClass();
        $user_id = Auth::user()->id;
        $current_user->id = $user_id;
        $current_user->givenname = Auth::user()->givenname;
        $current_user->familyname = Auth::user()->familyname;
        $this->view_data['all'] = static::all(['*'], [$user_id]);
        $this->view_data['current_user'] = $current_user;
        return $this->view_data;
    }

    /**
     * Adds the employers and officers for each row after running them through json_decode
     * @param type $columns
     * @param type $bindings
     * @param type $sort_fields
     * @return type
     */
    public static function all($columns = ['*'], $bindings = [], $sort_fields = []) {
        $all = parent::all($columns, $bindings, $sort_fields);
        $map = $all->map(function($item, $key) {
            $item->employers = json_decode($item->employers);
            $item->officers = json_decode($item->officers);
            return $item;
        });
        return $map->all();
    }

    /**
     * Adds the employers and officers for this row after running them through json_decode
     * @param Request $request
     * @param type $bindings
     * @return type
     */
    public function one($bindings = []) {
        $one = parent::one($bindings);
        $one->employers = json_decode($one->employers);
        $one->officers = json_decode($one->officers);
        return $one;
    }

    /**
     * Emits report_heading_list, officer_list and employer_list
     * @param type $bindings
     * @return array
     */
    public function getLookupTables($bindings = []) {
        $lookupReportHeadings = new LookupReportHeadings();
        $lookupEmployers = new LookupEmployers();
        $lookupOfficers = new LookupOfficers();
        $report_heading_list = $lookupReportHeadings->get();
        $employer_list = $lookupEmployers->get();
        $officer_list = $lookupOfficers->get();
        $lookup_tables = [
            'report_heading_list' => $report_heading_list,
            'officer_list' => $officer_list,
            'employer_list' => $employer_list
        ];
        return $lookup_tables;
    }

    /**
     * This deletes the report with the specified ID. This function calls
     * the PostgreSQL function 'delete_report'. If a function is called and not found,
     * an error is triggered, so this function checks if the function exists first before
     * running. If the function doesn't exist, a DataTableModelException is thrown.
     * @param type $id
     * @return type
     * @throws DataTableModelException
     */
    public function deleteReport($id) {
        $function_name = 'reports_one_delete';
        if (DbMetadata::functionExists(config('datatables.SCHEMA'), $function_name)) {
            $sql = 'SELECT * FROM ' . $function_name . '(' . $id . ')';
            $delete_report_result = DB::select($sql);
            return $delete_report_result;
        } else {
            throw new DataTableModelException('The function \'' . $function_name . '\' doesn\'t exist.');
        }
    }

    /**
     * Initialises employers_string and employers.
     */
    private function initEmployers() {
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
     * Initialises officers_string and officers.
     */
    private function initOfficers() {
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
                $control->setRequired([
                    'message' => 'Please choose an officer.',
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
     * Initialises event_date
     */
    private function initEventDate() {
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
     * Initialises report_heading_id.
     */
    private function initReportHeadingId() {
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
     * Initialises report_heading
     */
    private function initReportHeading() {
        $control_name = 'report_heading';
        $column = $this->getColumn($control_name);
        $column->setVisible(true);
        $this->setColumn($control_name, $column);
        $control = new DataControl($control_name);
        $this->setAddControl($control_name, $control);
        $this->setEditControl($control_name, $control);
    }

    /**
     * Initialises created_by_id
     */
    private function initCreatedById() {
        $control_name = 'created_by_id';
        $column = $this->getColumn($control_name);
        $column->setVisible(false);
        $control = new DataControl($control_name);
        $this->setEditControl($control_name, $control);
    }

    /**
     * Initialises created_by
     */
    private function initCreatedBy() {
        $control_name = 'created_by';
        $column = $this->getColumn($control_name);
        $column->setVisible(false);
        $control = new DataControl($control_name);
        $this->setEditControl($control_name, $control);
    }

    /**
     * Initialises modified_by_id
     */
    private function initModifiedById() {
        $control_name = 'modified_by_id';
        $column = $this->getColumn($control_name);
        $column->setVisible(false);
        $control = new DataControl($control_name);
        $this->setEditControl($control_name, $control);
    }

    /**
     * Initialises include
     */
    private function initInclude() {
        $control_name = 'include';
        $column = $this->getColumn($control_name);
        $column->setVisible(false);
    }

    /**
     * Initialises created_at
     */
    private function initCreatedAt() {
        $column_name = 'created_at';
        $column = $this->getColumn($column_name);
        $column->setTitle('Created');
        $column->setVisible(false);
        $column->setMoment(true);
        $this->setColumn($column_name, $column);
        $control = new DataControl($column_name);
        $this->setAddControl($column_name, $control);
        $this->setEditControl($column_name, $control);
    }

    /**
     * Updates the employers list for this report using the postgreSQL function reports_employers_update
     * @param type $employers
     * @param type $id
     * @return type
     * @throws DataTableModelException
     */
    public function updateEmployers($employers, $id) {
        $counter = 0;
        $function_name = 'reports_employers_update';
        $app_name = config('app.name');
        if (DbMetadata::functionExists(config($app_name . '.SCHEMA'), $function_name)) {
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
        $app_name = config('app.name');
        if (DbMetadata::functionExists(config($app_name . '.SCHEMA'), $function_name)) {
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
            $update_officers_result = DB::select($sql);
            return $update_officers_result[0];
        } else {
            throw new DataTableModelException('The function \'' . $function_name . '\' doesn\'t exist.');
        }
    }

    /**
     * Initialises updated_at
     */
    private function initUpdatedAt() {
        $column_name = 'updated_at';
        $column = $this->getColumn($column_name);
        $column->setTitle('Last Modified');
        $column->setMoment(true);
        $column->setVisible(false);
        $this->setColumn($column_name, $column);

        $control = new DataControl($column_name);
        $control->setType(DataControl::Readonly);
        $this->setAddControl($column_name, $control);
        $this->setEditControl($column_name, $control);
    }

    /**
     * Initialises modified_by
     */
    private function initModifiedBy() {
        $control_name = 'modified_by';
        $column = $this->getColumn($control_name);
        $column->setVisible(false);
        $control = new DataControl($control_name);
        $this->setEditControl($control_name, $control);
    }

    /**
     * Initialises description
     */
    protected function initDescription() {
        $control_name = 'description';
        $column = $this->getColumn($control_name);
        $column->setTemplateId('cropped_description');
        $column->setWidth('25%');
    }

    private function one_from_new_function($bindings = []) {
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
     * Processed a suitable object which can be sent to the client as a JSON string.
     * @param Request $request
     * @param string $directory
     * @param string $id
     * @return array
     * @throws DataTableModelException
     */
    public function handleSuccessfulUpdate(Request $request, $directory, $id) {
        try {
            $employers = $request->input("employers");
            $officers = $request->input("officers");
            $json = new \stdClass();
            $json->data = new \stdClass();
            if (!is_null($employers)) {
                $json->data->update_employers_result = $this->updateEmployers($employers, $id);
            }
            if (!is_null($officers)) {
                $json->data->update_officers_result = $this->updateOfficers($officers, $id);
            }
            $directory = $this->directory;
            $json->data->$directory = $this->one($request, [$id]);
            return json_encode($json);
        } catch (\Throwable $t) {
            throw new DataTableModelException($t->getMessage(), $t->getCode());
        }
    }

    public function new_one($bindings = []) {
        $one = $this->one_from_new_function($bindings);
        $one->employers = json_decode($one->employers);
        $one->officers = json_decode($one->officers);
        return $one;
    }

}
