<?php

namespace Tests\Unit;

use Tests\TestCase;
use Tests\TestHelpers;
use Illuminate\Routing\Route;
use App\Http\Controllers\ReadExcelFile;
use Box\Spout\Common\Entity\Cell;

//https://stackoverflow.com/questions/3841190/phpunit-fatal-error-handling/3842311
//These tests can cause fatal errors so run them with
//phpunit --process-isolation

class ImportExcel_est extends TestCase {

    public function _testImportExcelRoutes() {
        $this->assertTrue(TestHelpers::checkRoute('read_excel_file_batch_import/import'));
    }

    public function _testUploadedFilesConfig() {
        $app_name = config('app.name');
        $this->assertEquals($app_name, 'datatables', 'The app name is not datatables');
        $this->assertArrayHasKey('PATH_TO_EXCEL', config($app_name),
                'The models array in the datatables config file doesn\'t have\'PATH_TO_EXCEL\'');
        $this->assertArrayHasKey('PATH_TO_PDF', config($app_name),
                'The models array in the datatables config file doesn\'t have\'PATH_TO_PDF\'');
        //There are no models needed for the import routines. I use the controller directly.
    }

    public function _testReadExcelFileBatchImport() {
        $className = 'App\Http\Controllers\ReadExcelFileBatchImport';
        $this->assertTrue(class_exists($className), $className . ' doesn\'t exist.');
        if (class_exists($className)) {
            $msg = 'The getFullPathToExcel method should exist ' . $className;
            $this->assertTrue(method_exists($className, 'getFullPathToExcel'), $msg);
            $msg = 'The setUpperIndex method should exist ' . $className;
            $this->assertTrue(method_exists($className, 'setUpperIndex'), $msg);
            $msg = 'The setEventDate method should exist ' . $className;
            $this->assertTrue(method_exists($className, 'setEventDate'), $msg);
            $msg = 'The concatenateDescriptionRows method should exist ' . $className;
            $this->assertTrue(method_exists($className, 'concatenateDescriptionRows'), $msg);
            $msg = 'The separateHeadingFromDescription method should exist ' . $className;
            $this->assertTrue(method_exists($className, 'separateHeadingFromDescription'), $msg);
            $msg = 'The separateOfficersFromDescription method should exist ' . $className;
            $this->assertTrue(method_exists($className, 'separateOfficersFromDescription'), $msg);
            $msg = 'The setOfficers method should exist ' . $className;
            $this->assertTrue(method_exists($className, 'setOfficers'), $msg);
            $msg = 'The setEmployers method should exist ' . $className;
            $this->assertTrue(method_exists($className, 'setEmployers'), $msg);
            $msg = 'The setMergedModel method should exist ' . $className;
            $this->assertTrue(method_exists($className, 'setMergedModel'), $msg);
            $msg = 'The dateArray method should exist ' . $className;
            $this->assertTrue(method_exists($className, 'dateArray'), $msg);
            $msg = 'The import method should exist ' . $className;
            $this->assertTrue(method_exists($className, 'import'), $msg);
            $msg = 'The viewFile method should exist ' . $className;
            $this->assertTrue(method_exists($className, 'viewFile'), $msg);
            $msg = 'The get method should exist ' . $className;
            $this->assertTrue(method_exists($className, 'get'), $msg);
        }
    }

    protected function createRequest($uri, $method, $parameters = [], $cookies = [],
            $files = [], $server = [], $content
    ) {
        $request = new \Illuminate\Http\Request;
        return $request->createFromBase(
                        \Symfony\Component\HttpFoundation\Request::create(
                                $uri,
                                $method,
                                $parameters,
                                $cookies,
                                $files,
                                $server,
                                $content
                        )
        );
    }

    public function testReadExcelFileExists() {
        $className = 'App\Http\Controllers\ReadExcelFile';
        $this->assertTrue(class_exists($className), $className . ' doesn\'t exist.');
    }

    public function testReadExcelFileCheckForMultipleWorksheets() {
        $file_name = '2sheets.xlsx';
        $readExcelFile = new ReadExcelFile();
        try {
            $col_count = $readExcelFile->getColumnCount($file_name);
            $this->assertEquals(false, true, 'This test should throw an exception and it didn\'t.');
        } catch (\Exception $e) {
            $this->assertEquals('Make sure that the Excel file you are trying to import only has one worksheet.', $e->getMessage());
        }
    }

    public function testReadExcelFileGetColumnCount() {
        $file_name = 'Test Batch Import.xlsx';
        $readExcelFile = new ReadExcelFile();
        $col_count = $readExcelFile->getColumnCount($file_name);
        $this->assertEquals(4, $col_count, 'There are 4 columns in ' . $file_name);
    }

    public function _testReadExcelFileGetFullPathToExcel() {
        $file_name = 'Test Batch Import.xlsx';
        $className = 'App\Http\Controllers\ReadExcelFile';
        $msg = 'The getFullPathToExcel method should exist ' . $className;
        $exists = method_exists($className, 'getFullPathToExcel');
        $this->assertTrue($exists, $msg);
        if ($exists) {
            $readExcelFile = new ReadExcelFile();
            $full_path = $readExcelFile->getFullPathToExcel($file_name);
            $this->assertEquals('/var/www/gu/gudb0602/GeneralUnion/storage/exceldata/' . $file_name, $full_path);
        }
    }

    public function _testReadExcelFileDateCellExists() {
        $className = 'App\Http\Controllers\ReadExcelFile';
        $msg = 'The dateCellExists method should exist ' . $className;
        $exists = method_exists($className, 'dateCellExists');
        $this->assertTrue($exists, $msg);
        if ($exists) {
            $readExcelFile = new ReadExcelFile();
            //Create a new Cell with a date/time value. This should force the isDate method
            //to return true.
            $cell0 = new Cell(now());
            $cell1 = new Cell('test1');
            $cell2 = new Cell('test2');
            $cells = [$cell0, $cell1, $cell2];
            $cellExists = $readExcelFile->dateCellExists($cells);
            $this->assertEquals(true, $cellExists, 'Date Cell exists.');
        }
    }

    public function _testReadExcelFileGetColumnDefinitions() {
        $className = 'App\Http\Controllers\ReadExcelFile';
        $msg = 'The getColumnDefinitions method should exist ' . $className;
        $exists = method_exists($className, 'getColumnDefinitions');
        $this->assertTrue($exists, $msg);
    }

    public function _testReadExcelFileGetMethod() {
        $file_name = 'Test Batch Import.xlsx';
        $this->assertTrue(true);
        $readExcelFile = new ReadExcelFile();
        $excelData = $readExcelFile->get($file_name);
        $this->assertEquals(4, count($excelData->col_defs), 'There are 4 col_defs in ' . $file_name);
        $this->assertEquals(4, $excelData->column_count, 'There are 4 columns in ' . $file_name);
        $rows = $excelData->rows;
        $this->assertEquals(6, count($rows), 'There are 6 rows in ' . $file_name);
        $counter = 0;
        foreach ($rows as $index => $row) {
            $this->assertEquals($counter, $index, 'The index should increment by 1');
            //echo $row['rowIndex'];
            //fwrite(STDERR, print_r($counter . ' = ' . $index));
            $this->assertEquals($index, ($row['rowIndex'] - 1));
            $counter++;
        }
        $firstRow = $rows[0];
        $firstRowValues = $firstRow['values'];
        $this->assertEquals('Date', $firstRowValues[0], 'The 1st value in the first row is "Date"');
        $this->assertEquals('Employer/Branch/Organisation', $firstRowValues[1], 'The 2nd value in the first row is "Employer/Branch/Organisation"');
        $this->assertEquals('', $firstRowValues[2], 'The 3rd value in the first row is a zero-length string.');
        $this->assertEquals('Include in GUEC agenda document?', $firstRowValues[3], 'The 4th value in the first row is "Include in GUEC agenda document?".');

        $secondRow = $rows[1];
        $secondRowValues = $secondRow['values'];
        $this->assertEquals('2016-06-08 12:06:00', $secondRowValues[0], 'The 1st value in the 2nd row is "2016-06-08 12:06:00"');
        $this->assertEquals('Berlitz', $secondRowValues[1], 'The 2nd value in the 2nd row is "Berlitz"');
        $this->assertStringStartsWith('Consultation - Member', $secondRowValues[2], 'The 3rd value in the 2nd row starts with "Consultation - Member"');
        $this->assertStringEndsWith('We should  all be', $secondRowValues[2], 'The 3rd value in the 2nd row ends with "We should  all be"');

        $thirdRow = $rows[2];
        $thirdRowValues = $thirdRow['values'];
        $this->assertEquals('', $thirdRowValues[0], 'The 1st value in the 3rd row is a zero-length string.');
        $this->assertEquals('', $thirdRowValues[1], 'The 2nd value in the 3rd row is a zero-length string.');
        $this->assertStringStartsWith('receiving an apology payment', $thirdRowValues[2], 'The 3rd value in the 3rd row starts with "receiving an apology payment"');
        $this->assertStringEndsWith('Best  regards, George', $thirdRowValues[2], 'The 3rd value in the 3rd row ends with "Best  regards, George"');

        $fourthRow = $rows[3];
        $fourthRowValues = $fourthRow['values'];
        $this->assertEquals('2016-06-25 12:06:00', $fourthRowValues[0], 'The 1st value in the 4th row is "2016-06-25 12:06:00".');
        $this->assertEquals('ECC', $fourthRowValues[1], 'The 2nd value in the 4th row is . "ECC"');
        $this->assertStringStartsWith('Branch  Care  -  Branch  Meeting', $fourthRowValues[2], 'The 3rd value in the 4th row starts with "Branch  Care  -  Branch  Meeting"');
        $this->assertStringEndsWith('per  hour across  the board.', $fourthRowValues[2], 'The 3rd value in the 4th row ends with "per  hour across  the board."');

        $fifthRow = $rows[4];
        $fifthRowValues = $fifthRow['values'];
        $this->assertEquals('', $fifthRowValues[0], 'The 1st value in the 4th row is a zero-length string.');
        $this->assertEquals('', $fifthRowValues[1], 'The 2nd value in the 4th row is a zero-length string.');
        $this->assertStringStartsWith('If  we apply for mediation,', $fifthRowValues[2], 'The 3rd value in the 4th row starts with "If  we apply for mediation,"');
        $this->assertStringEndsWith('Perhaps  approach the  labor', $fifthRowValues[2], 'The 3rd value in the 4th row ends with "Perhaps  approach the  labor"');

        $sixthRow = $rows[5];
        $sixthRowValues = $sixthRow['values'];
        $this->assertEquals('', $sixthRowValues[0], 'The 1st value in the 4th row is a zero-length string.');
        $this->assertEquals('', $sixthRowValues[1], 'The 2nd value in the 4th row is a zero-length string.');
        $this->assertStringStartsWith('commission  later,', $sixthRowValues[2], 'The 3rd value in the 4th row starts with "commission  later,"');
        $this->assertStringEndsWith('regularly  with  GU executives. Budzowski', $sixthRowValues[2], 'The 3rd value in the 4th row ends with "regularly  with  GU executives. Budzowski"');
    }

    public function _testReadExcelFileBatchImportImportMethod() {
        //Request constructor parameters:
        /**
         * @param array                $query      The GET parameters
         * @param array                $request    The POST parameters
         * @param array                $attributes The request attributes (parameters parsed from the PATH_INFO, ...)
         * @param array                $cookies    The COOKIE parameters
         * @param array                $files      The FILES parameters
         * @param array                $server     The SERVER parameters
         * @param string|resource|null $content    The raw body data
         */
        $uri = 'configure_import_batch_import/Test Batch Import.xlsx';
        $method = 'POST';
        //In this test, for some reason, the simulated request doesn't register the post parameters,
        //only the get parameters
        $excel_values = [];
        $excel_values['1'] = new \stdClass();
        $excel_values['1']->event_date = '5\1\2020';

        $get_parameters = ['directory' => 'reports',
            'task_model' => 'Report',
            'server_side_key' => ['report_id'],
            'excel_fields' => [
                'event_date',
                'description',
                'report_heading',
                'employers_string'
            ],
            'excel_values' => $excel_values
        ];
        $post_parameters = [];
        //excel_values=%7B%221%22%3A%7B%22event_date%22%3A%225%2F1%2F2020%22%2C%22employers%22%3A%22Osaka+Gakuin+H.S.%22%2C%22description%22%3A%22Docs+-+OTHER++Reply+to+request+for+negotiations%5Cntest+1%22%7D%2C%222%22%3A%7B%22event_date%22%3A%225%2F1%2F2020%22%2C%22employers%22%3A%22Berlitz%22%2C%22description%22%3A%22Branch+Care+-+Branch+Meeting++TOPIC+%E2%80%93+Strike+Vote%5Cntest+2%5Cn%22%7D%2C%223%22%3A%7B%22event_date%22%3A%225%2F1%2F2020%22%2C%22employers%22%3A%22Berlitz%22%2C%22description%22%3A%22Docs+-+OTHER++New+membership+declaration+sent+for+Graeme+McNee.+Tesolat%5Cntest+3%22%7D%7D
        $attributes = [];
        $cookies = [];
        $files = [];
        $server = [
            'CONTENT_TYPE' => 'application/json',
            'REQUEST_URI' => $uri
        ];
        $content = null;
        $http_request = new \Illuminate\Http\Request(
                $get_parameters,
                $post_parameters,
                $attributes,
                $cookies,
                $files,
                $server
        );
        $http_request->setRouteResolver(function () use ($http_request, $method, $uri) {
            return (new Route($method, $uri, []))->bind($http_request);
        });

        //dd($http_request);
        $this->assertTrue($http_request->input('directory') === 'reports');
        $this->assertTrue($http_request->input('task_model') === 'Report');
        $this->assertTrue($http_request->input('server_side_key')[0] === 'report_id');
        //fwrite(STDERR, print_r(json_encode($http_request), TRUE));            
        /*
          dd($request);
         * 
         */
        //$className = 'App\Http\Controllers\ReadExcelFileBatchImport';
        //$batchImportMock = \Mockery::mock($className);
        //$batchImportMock->shouldReceive('import')->once()->andReturn(25);
    }

    public function _testStandardViewsJsCss() {
        $dirname = public_path('js/crud');
        $this->assertTrue(file_exists($dirname), $dirname . ' exists on the system.');
        if (file_exists($dirname)) {
            $files = [
                'namespace.js',
                'validation_rules.js',
                'get_scripts.js',
                'validation_rules.js',
                'error_handler.js',
                'primary_key.js',
                'view_model_standard.js',
                'data_table_factory.js',
                'data_table_factory_extend.js',
                'template_binding_context.js',
                'data_table_factory_templates.js',
                'edit.js',
                'selected_item.js',
                'selected_items.js',
                'startup.js',
                'knockout-datetimepicker.js'
            ];
            foreach ($files as $file) {
                $filename = $dirname . '/' . $file;
                $this->assertTrue(file_exists($filename), $filename . ' doesn\'t exist');
            }
        }
        $dirname = public_path('js/vendor');
        $this->assertTrue(file_exists($dirname), $dirname . ' exists on the system.');
        if (file_exists($dirname)) {
            $files = [
                'vendor.js',
                'app.js'
            ];
            foreach ($files as $file) {
                $filename = $dirname . '/' . $file;
                $this->assertTrue(file_exists($filename), $filename . ' doesn\'t exist');
            }
        }
        $dirname = public_path('js/import_excel');
        $this->assertTrue(file_exists($dirname), $dirname . ' exists on the system.');
        if (file_exists($dirname)) {
            $files = [
                'reports_task.js',
                'import_tasks.js',
                'fields_table_factory.js',
                'imported_data_table_factory.js',
                'excel_file_table_factory.js',
                'import_factory.js'
            ];
            foreach ($files as $file) {
                $filename = $dirname . '/' . $file;
                $this->assertTrue(file_exists($filename), $filename . ' doesn\'t exist');
            }
        }
    }

}
