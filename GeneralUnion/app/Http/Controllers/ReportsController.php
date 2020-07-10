<?php

/**
 * The ReportsController is a complex controller because it has to both administer the reports table
 * and handle imports from Excel files.
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Shared\Date as PhpOfficeDate;
use Carbon\Carbon;
use DB;
use App\AppClasses\DbMetadata;
use App\AppClasses\DataTableModelException;
use App\Traits\SSP;
use App\Events\ReportCreated;
/**
 * ReportsController provides server-side processing for the client-side data table because
 * the reports table is very large.
 *
 *
 * @author David Mann
 */
class ReportsController extends EditableDataTableController {

    use SSP;

    /**
     * Generates a downloadable Excel file with all the reports in the database.
     * @param Request $request
     */
    public function generateAllExcel(Request $request) {
        $select = "select * FROM reports_all_for_excel() ORDER BY event_date DESC;";
        $bindings = [];
        $this->generateExcel($select, $bindings);
    }

    /**
     * Generates a downloadable Excel file with event dates between the specified
     * from and to dates. Events on the from or to dates are not included. 
     * @param Request $request
     * @param type $from
     * @param type $to
     */
    public function generateFilteredExcel(Request $request, $from, $to, $single_column) {
        $select = "select * FROM reports_all_for_excel_from_to(to_date(?, 'Mon-DD-YYYY'),to_date(?,'Mon-DD-YYYY')) ORDER BY event_date DESC;";
        $bindings = [$from, $to];
        if ($single_column === true) {
            $this->generateOneColumnExcel($select, $bindings);
        } else {
            $this->generateExcel($select, $bindings);
        }
    }

    /**
     * Builds a report from a row of data.
     * @param type $row
     * @return string
     */
    private function buildReport($row) {
        $report = '';
        if ($row->report_heading) {
            $report .= $row->report_heading;
            if ($row->related_organisation) {
                $report .= " (" . $row->related_organisation . ")\n";
            } else {
                $report .= "\n";
            }
        } else {
            if ($row->related_organisation) {
                $report .= " (" . $row->related_organisation . ")\n";
            }
        }
        $report .= $row->description .
                "\n" .
                $row->officers;
        return $report;
    }

    /**
     * Generates a downloadable Excel file filtered by updated date. This filter is not inclusive
     * of the from or to dates.
     * @param Request $request
     * @param type $from
     * @param type $to
     */
    public function generateFilteredExcelByUpdatedDate(Request $request, $from, $to) {
        $select = "select * FROM reports_all_for_excel_by_updated_date_from_to(to_date(?, 'Mon-DD-YYYY'),to_date(?,'Mon-DD-YYYY')) ORDER BY event_date DESC;";
        $bindings = [$from, $to];
        $this->generateExcel($select, $bindings);
    }

    /**
     * Generates a downloadable Excel file of reports gathered with the specified
     * select query and bindings.
     * @param type $select
     * @param type $bindings
     */
    public function generateExcel($select, $bindings) {
        try {
            $excel = new Spreadsheet();
            $excel->getProperties()
                    ->setTitle('General Union Activity Reports ' . Carbon::now()->format('Y_M_d H:i:s'))
                    ->setCreator('General Union Administration Database')
                    ->setDescription('A list of GU activity reports.');
            $excel->setActiveSheetIndex(0);
            $worksheet = $excel->getActiveSheet()->setTitle('Activity Reports');
            $excel->getDefaultStyle()
                    ->getFont()
                    ->setName('Arial')
                    ->setSize(10);
            foreach (range('A', 'C') as $columnID) {
                $worksheet->getColumnDimension($columnID)
                        ->setWidth(13);
            }
            $worksheet->getStyle('E:E')->getAlignment()->setWrapText(true);
            foreach (range('D', 'D') as $columnID) {
                $worksheet->getColumnDimension($columnID)
                        ->setWidth(17);
            }
            foreach (range('E', 'E') as $columnID) {
                $worksheet->getColumnDimension($columnID)
                        ->setWidth(69);
            }
            foreach (range('F', 'F') as $columnID) {
                $worksheet->getColumnDimension($columnID)
                        ->setWidth(7);
            }
            $worksheet->getStyle('A:F')
                    ->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
            $timestamp_format = 'Y-m-d H:i:s';
            $row_index = 1;
            $raw_results = DB::select($select, $bindings);
            $results = array();
            $column_headings = new \stdClass();
            $column_headings->created_at = "First Created";
            $column_headings->updated_at = "Last Modified";
            $column_headings->event_date = "Event Date";
            $column_headings->employer_organisation = "Employer/Org/Event";
            $column_headings->report = "";
            $column_headings->include = "Include";
            array_push($results, (array) $column_headings);

            foreach ($raw_results as $row) {
                $report = new \stdClass();
                $created_at = null;
                if (!is_null($row->created_at)) {
                    $_created_at = Carbon::createFromFormat($timestamp_format, $row->created_at);
                    $created_at = $_created_at->format('Y/m/d');
                    $created_at = PhpOfficeDate::PHPToExcel($created_at);
                }
                $report->created_at = $created_at;
                $updated_at = null;
                if (!is_null($row->updated_at)) {
                    $_updated_at = Carbon::createFromFormat($timestamp_format, $row->created_at);
                    $updated_at = $_updated_at->format('Y/m/d');
                    $updated_at = PhpOfficeDate::PHPToExcel($updated_at);
                }
                $report->updated_at = $updated_at;
                $event_date = null;
                if (!is_null($row->event_date)) {
                    $_event_date = Carbon::createFromFormat($timestamp_format, $row->event_date);
                    $event_date = $_event_date->format('Y/m/d');
                    $event_date = PhpOfficeDate::PHPToExcel($event_date);
                }
                $report->event_date = $event_date;
                //https://github.com/PHPOffice/PhpSpreadsheet/issues/156
                //Use double quotes to add a new line character to a string in PHP
                //Single quotes will give you a literal \n
                $report->employer_organisation = $row->employers;
                $report->report = $this->buildReport($row);
                $include = 'NO';
                if ($row->include) {
                    $include = 'YES';
                }
                $report->include = $include;
                array_push($results, (array) $report);
                $row_index++;
            }
            $worksheet->fromArray($results, null, 'A1', false, false);
            //I started $row_index at 1 to skip the header row.
            //Don't forget to add 1 to the delimiter to make
            //sure the formatting reaches the final row not the penultimate row.
            for ($i = 1; $i < $row_index + 1; $i++) {
                $worksheet->getStyle('A' . $i)
                        ->getNumberFormat()
                        ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_YYYYMMDD2);
                $worksheet->getStyle('B' . $i)
                        ->getNumberFormat()
                        ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_YYYYMMDD2);
                $worksheet->getStyle('C' . $i)
                        ->getNumberFormat()
                        ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_YYYYMMDD2);
            }
            $last_modified = Carbon::now()->format('Y_M_d H:i:s');
            $extension = 'Xlsx';
            //I think you have to create the writer before adding the headers.
            $writer = IOFactory::createWriter($excel, $extension);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            //header("Content-Disposition: attachment; filename=\"fileName.{$extension}\"");
            $last_modified = Carbon::now()->format('YMd H:i:s');
            header('Content-Disposition: attachment;filename="General_Union_Activity_Reports_' . $last_modified . '.xlsx"');
            $writer->save('php://output');
        } catch (Exception $e) {
            $this->handleEchoException($e);
        }
    }

    public function generateOneColumnExcel($select, $bindings) {
        try {
            $excel = new Spreadsheet();
            $excel->getProperties()
                    ->setTitle('General Union Activity Reports ' . Carbon::now()->format('Y_M_d H:i:s'))
                    ->setCreator('General Union Administration Database')
                    ->setDescription('A list of GU activity reports.');
            $excel->setActiveSheetIndex(0);
            $worksheet = $excel->getActiveSheet()->setTitle('Activity Reports');
            $excel->getDefaultStyle()
                    ->getFont()
                    ->setName('Arial')
                    ->setSize(10);
            /*
              foreach (range('A', 'C') as $columnID) {
              $worksheet->getColumnDimension($columnID)
              ->setWidth(13);
              }
              $worksheet->getStyle('E:E')->getAlignment()->setWrapText(true);

              foreach (range('D', 'D') as $columnID) {
              $worksheet->getColumnDimension($columnID)
              ->setWidth(17);
              }
              foreach (range('E', 'E') as $columnID) {
              $worksheet->getColumnDimension($columnID)
              ->setWidth(69);
              }
              foreach (range('F', 'F') as $columnID) {
              $worksheet->getColumnDimension($columnID)
              ->setWidth(7);
              }
              $worksheet->getStyle('A:F')
              ->getAlignment()
              ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)
              ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
              //$function_name = 'get_reports_for_excel';
             * 
             */
            $worksheet->getStyle('A:A')->getAlignment()->setWrapText(true);
            foreach (range('A', 'A') as $columnID) {
                $worksheet->getColumnDimension($columnID)
                        ->setWidth(69);
            }
            $worksheet->getStyle('A:A')
                    ->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
            $timestamp_format = 'Y-m-d H:i:s';
            $row_index = 1;
            //if (DbMetadata::functionExists(config('gu.SCHEMA'), $function_name)) {
            $raw_results = DB::select($select, $bindings);
            $results = array();
            /*
             * We don't need column headings
              $column_headings = new \stdClass();
              $column_headings->created_at = "First Created";
              $column_headings->updated_at = "Last Modified";
              $column_headings->event_date = "Event Date";
              $column_headings->employer_organisation = "Employer/Org/Event";
              $column_headings->report = "";
              $column_headings->include = "Include";
              array_push($results, (array) $column_headings);
             * 
             */
            foreach ($raw_results as $row) {
                $report = new \stdClass();
                /*
                  $created_at = null;
                  if (!is_null($row->created_at)) {
                  $_created_at = Carbon::createFromFormat($timestamp_format, $row->created_at);
                  $created_at = $_created_at->format('Y/m/d');
                  $created_at = PhpOfficeDate::PHPToExcel($created_at);
                  }
                  $report->created_at = $created_at;

                  $updated_at = null;
                  if (!is_null($row->updated_at)) {
                  $_updated_at = Carbon::createFromFormat($timestamp_format, $row->created_at);
                  $updated_at = $_updated_at->format('Y/m/d');
                  $updated_at = PhpOfficeDate::PHPToExcel($updated_at);
                  }
                  $report->updated_at = $updated_at;
                 */
                $event_date = null;
                if (!is_null($row->event_date)) {
                    $_event_date = Carbon::createFromFormat($timestamp_format, $row->event_date);
                    //$event_date = $_event_date;
                    $event_date = $_event_date->format('Y/m/d');
                    //We can't specify a format for this single column worksheet and for some reason,
                    //the next line has an intermittent bug and displays the date wrong.
                    //$event_date = PhpOfficeDate::PHPToExcel($event_date);
                }
                //Event date
                $report->data = $row->employers;
                //array_push($results, (array) $report);
                //https://github.com/PHPOffice/PhpSpreadsheet/issues/156
                //Use double quotes to add a new line character to a string in PHP
                //Single quotes will give you a literal \n
                //Employer organisation
                //array_push($results, (array) $report);
                $report->data .= "\n";
                $report->data .= $event_date . "\n";
                $report->data .= $this->buildReport($row);
                array_push($results, (array) $report);
                /*
                  $include = 'NO';
                  if ($row->include) {
                  $include = 'YES';
                  }
                  $report->include = $include;
                 * 
                 */
                $row_index++;
            }
            //} else {
            //    throw new DataTableModelException('The function \'' . $function_name . '\' doesn\'t exist.');
            //}

            $worksheet->fromArray($results, null, 'A1', false, false);
            //I started $row_index at 1 to skip the header row.
            //Don't forget to add 1 to the delimiter to make
            //sure the formatting reaches the final row not the penultimate row.
            for ($i = 1; $i < $row_index + 1; $i++) {
                $worksheet->getStyle('A' . $i)
                        ->getNumberFormat()
                        ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_YYYYMMDD2);
                $worksheet->getStyle('B' . $i)
                        ->getNumberFormat()
                        ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_YYYYMMDD2);
                $worksheet->getStyle('C' . $i)
                        ->getNumberFormat()
                        ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_YYYYMMDD2);
            }
            $last_modified = Carbon::now()->format('Y_M_d H:i:s');
            $extension = 'Xlsx';
            //I think you have to create the writer before adding the headers.
            $writer = IOFactory::createWriter($excel, $extension);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            //header("Content-Disposition: attachment; filename=\"fileName.{$extension}\"");
            $last_modified = Carbon::now()->format('YMd H:i:s');
            header('Content-Disposition: attachment;filename="General_Union_Activity_Reports_' . $last_modified . '.xlsx"');
            $writer->save('php://output');
        } catch (Exception $e) {
            $this->handleEchoException($e);
        }
    }

    /**
     * Returns a JSON-encoded string of all available employers for a given report.
     * @param Request $request
     * @param string $report_id
     * @return string
     */
    public function getAvailableEmployers(Request $request, $report_id) {
        $report = new Report();
        return json_encode($report->getAvailableEmployers($report_id));
    }

    /**
     * Returns a JSON-encoded string of all available officers for a given report.
     * @param Request $request
     * @param type $report_id
     * @return type
     */
    public function getAvailableOfficers(Request $request, $report_id) {
        $report = new Report();
        return json_encode($report->getAvailableOfficers($report_id));
    }

    /**
     * Updates the employers list for a report.
     * @param type $employers
     * @param type $id
     * @return type
     */
    private function updateEmployers($employers, $id) {
        return $this->model->updateEmployers($employers, $id);
    }

    /**
     * Updates the officers for a given report.
     * @param type $officers
     * @param type $id
     * @return type
     */
    private function updateOfficers($officers, $id) {
        return $this->model->updateOfficers($officers, $id);
    }

    /**
     * Gather the data required to send a response to the client after successfully
     * adding a new report.
     * @param Request $request
     * @param string $id
     * @return string JSON encoded
     */
    public function handleSuccessfulStore(Request $request, $id) {
        try {
            Report::created(function(){
                return 'created';
            });
            //event(new ReportCreated($id));
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
            $model = new Report();
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
     * @return type
     */
    public function delete(Request $request) {
        try {
            $id = $request->report_id;
            $report = new Report();
            $raw_result = $report->deleteReport($id);
            $result = $raw_result[0]->reports_one_delete;
            return $this->handleSuccessfulDelete($request, $result);
        } catch (\Throwable $t) {
            return $this->handleException($t);
        }
    }

}
