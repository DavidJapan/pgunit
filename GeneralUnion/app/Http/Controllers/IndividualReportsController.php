<?php

/**
 * The IndividualReportsController is a less complex controller than ReportsController because it
 * only administers reports owned by the current user.
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IndividualReport;
use Carbon\Carbon;
use DB;
use App\AppClasses\DbMetadata;
use App\AppClasses\DataTableModelException;
use App\Traits\SSP;

/**
 * IndividualReportsController is simpler than ReportsController and so it extends EditableDataTableController directly.
 * It handles the relatively simple task of dealing with requests by a user to edit and insert their own reports.
 *
 *
 * @author David Mann
 */
class IndividualReportsController extends EditableDataTableController {

    /**
     * Returns a JSON-encoded string representing an array of employers available for this report.
     * @param Request $request
     * @param string $report_id
     * @return string  JSON-encoded string of the employers available for this report.
     */
    public function getAvailableEmployers(Request $request, $report_id) {
        $report = new IndividualReport();
        return json_encode($report->getAvailableEmployers($report_id));
    }
    /**
     * Updates the employers associated with this report.
     * @param array $employers
     * @param string $id
     * @return array
     */
    private function updateEmployers($employers, $id) {
        return $this->model->updateEmployers($employers, $id);
    }
    /**
     * Updates the officers associated with this report.
     * @param array $officers
     * @param string $id
     * @return array
     */
    private function updateOfficers($officers, $id) {
        return $this->model->updateOfficers($officers, $id);
    }
    /**
     * This gathers the data necessary to pass to the client after
     * successfully adding a new report.
     * @param Request $request
     * @param string $id
     * @return string JSON-encoded string representing the newly added report.
     */
    public function handleSuccessfulStore(Request $request, $id) {
        try {
            $json = new \stdClass();
            $json->data = new \stdClass();
            $employers = $request->input("employers");
            $officers = $request->input("officers");
            if (!is_null($employers)) {
                $json->data->update_employers_result = $this->updateEmployers($employers, $id);
            }
            if (!is_null($officers)) {
                $json->data->update_officers_result = $this->updateOfficers($officers, $id);
            }
            $directory = $this->model->directory();
            $model = new IndividualReport();
            //We use the instance method here because we want to call the one_function if it exists.
            $json->data->$directory = $model->new_one($request, [$id]);
            return json_encode($json);
        } catch (\Throwable $t) {
            return $this->handleException($t);
        }
    }

    /**
     * Returns a JSON-encoded string with the boolean property result for success or failure.
     * @param Request $request
     * @return string JSON-encoded string with the boolean property result.
     */
    public function delete(Request $request) {
        try {
            $id = $request->report_id;
            $report = new IndividualReport();
            $raw_result = $report->deleteReport($id);
            $result = $raw_result[0]->reports_one_delete;
            return $this->handleSuccessfulDelete($request, $result);
        } catch (\Throwable $t) {
            return $this->handleException($t);
        }
    }

}
