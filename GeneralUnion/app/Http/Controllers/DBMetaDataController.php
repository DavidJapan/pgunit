<?php
/**
 * Provides the methods for running pgunit tests on postgreSQL functions.
 */
namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

/**
 * pgunit provides a clean and simple way of testing postgreSQL functions in their native environment.
 * This controller allows you to run them against the current database. Obviously the basic connection
 * between this app and the database must be established before this can happen.
 */
class DBMetaDataController extends Controller {

    /**
     * Calls the postgreSQL function function_column_data_types_all_get to get the column
     * data types for the specified function in the specified schema.
     * @param Request $request
     * @param type $schema
     * @param type $function
     * @return string representing an HTML table
     */
    public static function getFunctionColumnDataTypes(Request $request, $schema = 'public', $function) {
        $sql = <<<SQL
    SELECT * FROM function_column_data_types_all_get('$schema', '$function');
SQL;
        $return = DB::select($sql, []);
        return $return;
    }
    /**
     * Builds a string representing an HTML table from an array of objects. 
     * @param array $data
     * @return string representing an HTML table
     */
    private function buildDisplayTable($data){
        $output = '<table>';
        foreach ($data as $key => $var) {
            //$output .= $key . '<br>';
            if ($key === 0) {
                $output .= '<tr>';
                foreach ($var as $k => $v) {
                    $output .= '<th>' . $k . '</th>';
                }
                $output .= '</tr>';
            }
        }
        foreach ($data as $key => $var) {
            $output .= '<tr ';
            if($var->failed === true || $var->erroneous === true){
                $output .= 'class="table-danger" ';
            }else{
                $output .= 'class="table-success" ';
            }
            $output .= '>';
            foreach ($var as $k => $v) {
                $output .= '<td>';
                if ($v === false) {
                    $v = 'false';
                } else if ($v === true) {
                    $v = 'true';
                }
                $output .= $v . '</td>';
            }
            $output .= '</tr>';
        }
        $output .= '</table>';
        return $output;
    }
    /**
     * Tests all the test available in the Postgres database.
     * @return view
     */
    public function pgunitTestAll() {
        $select = 'select * from pgunit.test_run_all()';
        $data = DB::select($select, []);
        $output = $this->buildDisplayTable($data);
        //dd($data);
        //dd($data);
        return response()->view('/tables/pgunit', ['view_data' => $output, 'group' => 'All Groups'], 200);
    }
    /**
     * Tests all the test in the users suite.
     * @return view
     */
    public function pgunitTestSchema() {
        $select = 'select * from pgunit.test_run_suite(\'schema\')';
        $data = DB::select($select, []);
        $output = $this->buildDisplayTable($data);
        return response()->view('/tables/pgunit', ['view_data' => $output, 'group' => 'Schema'], 200);
    }
    /**
     * Tests all the test in the users suite.
     * @return view
     */
    public function pgunitTestUsers() {
        $select = 'select * from pgunit.test_run_suite(\'users\')';
        $data = DB::select($select, []);
        $output = $this->buildDisplayTable($data);
        return response()->view('/tables/pgunit', ['view_data' => $output, 'group' => 'Users'], 200);
    }
    /**
     * Tests all the test in the roles suite.
     * @return view
     */
    public function pgunitTestRoles() {
        $select = 'select * from pgunit.test_run_suite(\'roles\')';
        $data = DB::select($select, []);
        $output = $this->buildDisplayTable($data);
        return response()->view('/tables/pgunit', ['view_data' => $output, 'group' => 'Roles'], 200);
    }
    /**
     * Tests all the test in the sectors suite.
     * @return view
     */
    public function pgunitTestSectors() {
        $select = 'select * from pgunit.test_run_suite(\'sectors\')';
        $data = DB::select($select, []);
        $output = $this->buildDisplayTable($data);
        return response()->view('/tables/pgunit', ['view_data' => $output, 'group' => 'Sectors'], 200);
    }
    /**
     * Tests all the test in the branches suite.
     * @return view
     */
    public function pgunitTestBranches() {
        $select = 'select * from pgunit.test_run_suite(\'branches\')';
        $data = DB::select($select, []);
        $output = $this->buildDisplayTable($data);
        return response()->view('/tables/pgunit', ['view_data' => $output, 'group' => 'Branches'], 200);
    }
    /**
     * Tests all the test in the individual_reports suite.
     * @return view
     */
    public function pgunitTestIndividualReports() {
        $select = 'select * from pgunit.test_run_suite(\'individual_reports\')';
        $data = DB::select($select, []);
        $output = $this->buildDisplayTable($data);
        return response()->view('/tables/pgunit', ['view_data' => $output, 'group' => 'Individual Reports'], 200);
    }
    /**
     * Tests all the test in the reports suite.
     * @return view
     */
    public function pgunitTestReports() {
        $select = 'select * from pgunit.test_run_suite(\'reports\')';
        $data = DB::select($select, []);
        $output = $this->buildDisplayTable($data);
        return response()->view('/tables/pgunit', ['view_data' => $output, 'group' => 'Reports'], 200);
    }
    /**
     * Tests all the test in the employers suite.
     * @return view
     */
    public function pgunitTestEmployers() {
        $select = 'select * from pgunit.test_run_suite(\'employers\')';
        $data = DB::select($select, []);
        $output = $this->buildDisplayTable($data);
        return response()->view('/tables/pgunit', ['view_data' => $output, 'group' => 'Employers'], 200);
    }
    /**
     * Tests all the test in the report_headings suite.
     * @return view
     */
    public function pgunitTestReportHeadings() {
        $select = 'select * from pgunit.test_run_suite(\'report_headings\')';
        $data = DB::select($select, []);
        $output = $this->buildDisplayTable($data);
        return response()->view('/tables/pgunit', ['view_data' => $output, 'group' => 'Report Headings'], 200);
    }
    /**
     * Tests all the test in the collective_agreements suite.
     * @return view
     */
    public function pgunitTestCollectiveAgreements() {
        $select = 'select * from pgunit.test_run_suite(\'collective_agreements\')';
        $data = DB::select($select, []);
        $output = $this->buildDisplayTable($data);
        return response()->view('/tables/pgunit', ['view_data' => $output, 'group' => 'Collective Agreements'], 200);
    }
}
