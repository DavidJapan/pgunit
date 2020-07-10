<?php

namespace Tests\Unit;

use Tests\TestCase;
use Tests\TestHelpers;
use Illuminate\Routing\Route;
use App\Http\Controllers\ReadExcelFile;
use Box\Spout\Common\Entity\Cell;
use DB;

//https://stackoverflow.com/questions/3841190/phpunit-fatal-error-handling/3842311
//These tests can cause fatal errors so run them with
//phpunit --process-isolation

class ReadExcelFileTest extends TestCase {

    public function testImportExcelRoutes() {
        $this->assertTrue(TestHelpers::checkRoute('read_excel_file/import'));
    }

    public function testUploadedFilesConfig() {
        $app_name = config('app.name');
        $this->assertEquals($app_name, 'datatables', 'The app name is not datatables');
        $this->assertArrayHasKey('PATH_TO_EXCEL', config($app_name),
                'The models array in the datatables config file doesn\'t have\'PATH_TO_EXCEL\'');
        $this->assertArrayHasKey('PATH_TO_PDF', config($app_name),
                'The models array in the datatables config file doesn\'t have\'PATH_TO_PDF\'');
        //There are no models needed for the import routines. I use the controller directly.
    }

    public function testReadExcelFile() {
        $className = 'App\Http\Controllers\ReadExcelFile';
        $this->assertTrue(class_exists($className), $className . ' doesn\'t exist.');
        if (class_exists($className)) {
            $msg = 'The getFullPathToExcel method should exist ' . $className;
            $this->assertTrue(method_exists($className, 'getFullPathToExcel'), $msg);
            $msg = 'The setUpperIndex method should exist ' . $className;
            $this->assertTrue(method_exists($className, 'setUpperIndex'), $msg);
            $msg = 'The setValue method should exist ' . $className;
            $this->assertTrue(method_exists($className, 'setValue'), $msg);
            $msg = 'The concatenateDescriptionRows method should exist ' . $className;
            $this->assertTrue(method_exists($className, 'concatenateDescriptionRows'), $msg);
            $msg = 'The separateHeadingFromDescription method should exist ' . $className;
            $this->assertTrue(method_exists($className, 'separateHeadingFromDescription'), $msg);
            $msg = 'The extractOfficersFromDescription method should exist ' . $className;
            $this->assertTrue(method_exists($className, 'extractOfficersFromDescription'), $msg);
            $msg = 'The setOfficers method should exist ' . $className;
            $this->assertTrue(method_exists($className, 'setOfficers'), $msg);
            $msg = 'The setEmployers method should exist ' . $className;
            $this->assertTrue(method_exists($className, 'setEmployers'), $msg);
            $msg = 'The import method should exist ' . $className;
            $this->assertTrue(method_exists($className, 'import'), $msg);
            $msg = 'The viewFile method should exist ' . $className;
            $this->assertTrue(method_exists($className, 'viewFile'), $msg);
            $msg = 'The getForDisplay method should exist ' . $className;
            $this->assertTrue(method_exists($className, 'getForDisplay'), $msg);
        }
    }

    /*
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
     * 
     */

    public function testReadExcelFileExists() {
        $className = 'App\Http\Controllers\ReadExcelFile';
        $this->assertTrue(class_exists($className), $className . ' doesn\'t exist.');
    }

    public function testCheckForMultipleWorksheets() {
        $file_name = '2sheets.xlsx';
        $readExcelFile = new ReadExcelFile();
        try {
            $col_count = $readExcelFile->getColumnCount($file_name);
            $this->assertEquals(false, true, 'This test should throw an exception and it didn\'t.');
        } catch (\Exception $e) {
            $this->assertEquals('Make sure that the Excel file you are trying to import only has one worksheet.', $e->getMessage());
        }
    }

    public function testGetColumnCount() {
        $file_name = 'Test Batch Import.xlsx';
        $readExcelFile = new ReadExcelFile();
        $col_count = $readExcelFile->getColumnCount($file_name);
        $this->assertEquals(4, $col_count, 'There are 4 columns in ' . $file_name);
    }

    public function testGetFullPathToExcel() {
        $file_name = 'Test Batch Import.xlsx';
        $className = 'App\Http\Controllers\ReadExcelFile';
        $msg = 'The getFullPathToExcel method should exist ' . $className;
        $exists = method_exists($className, 'getFullPathToExcel');
        $this->assertTrue($exists, $msg);
        if ($exists) {
            $readExcelFile = new ReadExcelFile();
            $full_path = $readExcelFile->getFullPathToExcel($file_name);
            $this->assertEquals('/var/www/gu/gudb0603/GeneralUnion/storage/exceldata/' . $file_name, $full_path);
        }
    }

    public function testGetDateCellIndex() {
        $className = 'App\Http\Controllers\ReadExcelFile';
        $msg = 'The getDateCellIndex method should exist ' . $className;
        $exists = method_exists($className, 'getDateCellIndex');
        $this->assertTrue($exists, $msg);
        if ($exists) {
            $readExcelFile = new ReadExcelFile();
            //Create a new Cell with a date/time value. This should force the isDate method
            //to return true.
            $cell0 = new Cell(now());
            $cell1 = new Cell('test1');
            $cell2 = new Cell('test2');
            $cells = [$cell0, $cell1, $cell2];
            $dateCellIndex = $readExcelFile->getDateCellIndex($cells);
            $this->assertEquals(0, $dateCellIndex, 'Date Cell exists.');
        }
    }

    public function testGetColumnDefinitions() {
        $file_name = 'Test Batch Import.xlsx';
        $className = 'App\Http\Controllers\ReadExcelFile';
        $msg = 'The getColumnDefinitions method should exist ' . $className;
        $exists = method_exists($className, 'getColumnDefinitions');
        $this->assertTrue($exists, $msg);
        if ($exists) {
            $readExcelFile = new ReadExcelFile();
            $colDefs = $readExcelFile->getColumnDefinitions($file_name);
            $this->assertEquals(4, count($colDefs), 'There are 4 column definitions.');
            $this->assertEquals(true, $colDefs[0]->moment, 'The first column is a date.');
            $this->assertEquals(false, $colDefs[1]->moment, 'The 2nd column is not a date.');
            $this->assertEquals(false, $colDefs[2]->moment, 'The first column is not a date.');
            $this->assertEquals(false, $colDefs[3]->moment, 'The first column is not a date.');
        }
    }

    public function testGetForDisplayMethod() {
        $file_name = 'Test Batch Import.xlsx';
        $readExcelFile = new ReadExcelFile();
        $excelData = $readExcelFile->getForDisplay($file_name);
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
        $this->assertEquals('2016-06-08', $secondRowValues[0], 'The 1st value in the 2nd row is "2016-06-08"');
        $this->assertEquals('Berlitz', $secondRowValues[1], 'The 2nd value in the 2nd row is "Berlitz"');
        $this->assertStringStartsWith('Consultation - Member', $secondRowValues[2], 'The 3rd value in the 2nd row starts with "Consultation - Member"');
        $this->assertStringEndsWith('We should  all be', $secondRowValues[2], 'The 3rd value in the 2nd row ends with "We should  all be"');

        $thirdRow = $rows[2];
        $thirdRowValues = $thirdRow['values'];
        $this->assertEquals('', $thirdRowValues[0], 'The 1st value in the 3rd row is a zero-length string.');
        $this->assertEquals('', $thirdRowValues[1], 'The 2nd value in the 3rd row is a zero-length string.');
        $this->assertStringStartsWith('receiving an apology payment', $thirdRowValues[2], 'The 3rd value in the 3rd row starts with "receiving an apology payment"');
        $this->assertStringEndsWith('DT, IR', $thirdRowValues[2], 'The 3rd value in the 3rd row ends with "Best  regards, George"');

        $fourthRow = $rows[3];
        $fourthRowValues = $fourthRow['values'];
        $this->assertEquals('2016-06-25', $fourthRowValues[0], 'The 1st value in the 4th row is "2016-06-25".');
        $this->assertEquals('ECC', $fourthRowValues[1], 'The 2nd value in the 4th row is . "ECC"');
        $this->assertStringStartsWith('Branch Care - Branch Meeting', $fourthRowValues[2], 'The 3rd value in the 4th row starts with "Branch Care - Branch Meeting"');
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

    public function testRequestInputs() {
        $uri = 'configure_import_batch_import/Test Batch Import.xlsx';
        $method = 'POST';
        $get_parameters = [
            'directory' => 'reports',
            'task_model' => 'Report',
            'file_name' => 'Test Batch Import.xlsx',
            'fields' => [
                'event_date' => 0,
                'description' => 1,
                'report_heading' => 2
            ]
        ];
        $post_parameters = [];
        $server = [
            'CONTENT_TYPE' => 'application/json',
            'REQUEST_URI' => $uri
        ];
        $request = $this->createRequest($uri, $method, $get_parameters, $server);
        $readExcelFile = new ReadExcelFile();
        $taskModel = $readExcelFile->getTaskModel($request);
        $this->assertEquals($taskModel, 'Report');
        $directory = $readExcelFile->getDirectory($request);
        $this->assertEquals($directory, 'reports');
        $fileName = $readExcelFile->getFileName($request);
        $this->assertEquals($fileName, 'Test Batch Import.xlsx');
        $fields = $readExcelFile->getFields($request);
        $this->assertEquals(count($fields), 3);
    }

    public function testRequest() {
        $uri = 'configure_import_batch_import/Test Batch Import.xlsx';
        $method = 'POST';
        $get_parameters = ['directory' => 'reports',
            'task_model' => 'Report',
            'server_side_key' => ['report_id'],
            'fields' => [
                'event_date' => 0,
                'description' => 1,
                'report_heading' => 2
            ]
        ];
        $server = [
            'CONTENT_TYPE' => 'application/json',
            'REQUEST_URI' => $uri
        ];
        $request = $this->createRequest($uri, $method, $get_parameters, $server);
        $all = $request->all();
        //dd($all);
        $fields = $all['fields'];
        $this->assertEquals($all['directory'], 'reports', 'The directory is reports.');
        $this->assertEquals($all['task_model'], 'Report', 'The task_model is Report.');
        $this->assertEquals($fields['event_date'], '0', 'The first field is 0.');
        $this->assertEquals($fields['description'], '1', 'The 2nd field is 1.');
        $this->assertEquals($fields['report_heading'], '2', 'The 3rd field is 2.');
    }

    public function testImportMethod() {
        $file_name = 'Test Batch Import.xlsx';
        $className = 'App\Http\Controllers\ReadExcelFile';
        $msg = 'The import method should exist ' . $className;
        $exists = method_exists($className, 'import');
        $this->assertTrue($exists, $msg);
        if ($exists) {
            $uri = 'configure_import_batch_import/Test Batch Import.xlsx';
            $method = 'POST';
            $get_parameters = ['directory' => 'reports',
                'task_model' => 'Report',
                'file_name' => $file_name,
                'fields' => [
                    'event_date' => 0,
                    'employers' => 1,
                    'description' => 2,
                    'related_organisation' => 3
                ]
            ];
            $server = [
                'CONTENT_TYPE' => 'application/json',
                'REQUEST_URI' => $uri
            ];
            $request = $this->createRequest($uri, $method, $get_parameters, $server);
            $readExcelFile = new ReadExcelFile();
            $fields = $readExcelFile->getFields($request);
            //echo json_encode($fields) . PHP_EOL;
            $this->assertEquals($fields['event_date'], 0);
            $this->assertEquals($fields['description'], 2);
            $this->assertEquals($fields['employers'], 1);
            try {
                //https://medium.com/yish/how-to-mock-authentication-user-on-unit-test-in-laravel-1441d491d82c
                $userModel = new \App\Models\User;
                $user = $userModel->find(1);
                $this->be($user);
                $import = $readExcelFile->import($request);
                $object = json_decode($import);
                $this->assertEquals($object->count, 2);
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }
    }

    /**
     * @param array                $get_parameters      The GET parameters
     * @param array                $request    The POST parameters
     * @param array                $server     The SERVER parameters
     * @param array                $attributes The request attributes (parameters parsed from the PATH_INFO, ...)
     * @param array                $cookies    The COOKIE parameters
     * @param array                $files      The FILES parameters
     * @param string|resource|null $content    The raw body data
     */
    public function createRequest($uri, $method, $get_parameters, $server, $post_parameters = [], $attributes = [], $cookies = [], $files = [], $content = null) {
        //Request constructor parameters:
        //In this test, for some reason, the simulated request doesn't register the post parameters,
        $http_request = new \Illuminate\Http\Request(
                $get_parameters,
                $post_parameters,
                $attributes,
                $cookies,
                $files,
                $server,
                $content
        );
        $http_request->setRouteResolver(function () use ($http_request, $method, $uri) {
            return (new Route($method, $uri, []))->bind($http_request);
        });
        return $http_request;
        //dd($http_request);
        //$this->assertTrue($http_request->input('directory') === 'reports');
        //$this->assertTrue($http_request->input('task_model') === 'Report');
        //$this->assertTrue($http_request->input('server_side_key')[0] === 'report_id');
        //fwrite(STDERR, print_r(json_encode($http_request), TRUE));            
    }

    public function testGetDateArray() {
        $file_name = 'Test Batch Import.xlsx';
        $className = 'App\Http\Controllers\ReadExcelFile';
        $msg = 'The import getDateArray should exist ' . $className;
        $exists = method_exists($className, 'getDateArray');
        $this->assertTrue($exists, $msg);
        if ($exists) {
            $readExcelFile = new ReadExcelFile();
            $dates = $readExcelFile->getDateArray($file_name);
            $this->assertEquals(2, count($dates));
            $this->assertEquals('2016-06-08', $dates[0]->date);
            $this->assertEquals('1', $dates[0]->index);
            $this->assertEquals('2', $dates[0]->upper_index);
            $this->assertEquals('2016-06-25', $dates[1]->date);
            $this->assertEquals('3', $dates[1]->index);
            $this->assertEquals('5', $dates[1]->upper_index);
        }
    }

    public function testStandardViewsJsCss() {
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

    public function testConvertExcelToValuesArray() {
        $file_name = 'Test Batch Import.xlsx';
        $readExcelFile = new ReadExcelFile();
        $fields = [
            'event_date' => 0,
            'employers' => 1,
            'description' => 2
        ];
        $valuesArray = $readExcelFile->convertExcelToValuesArray($file_name, $fields);
        $this->assertEquals(6, count($valuesArray));
        $this->assertEquals('2016-06-08', $valuesArray[1]->event_date);
        echo 'Employers 1: ' . $valuesArray[1]->employers . PHP_EOL;
        $this->assertEquals('2016-06-25', $valuesArray[3]->event_date);
        echo 'Employers 2: ' . $valuesArray[3]->employers . PHP_EOL;
    }

    public function testBuildModelsArray() {
        $delete = 'DELETE FROM reports where report_id >= 8196';
        DB::delete($delete);
        $seq_val_sql = "select setval(pg_get_serial_sequence('reports', 'report_id'), 8196, true);";
        $seq_val = DB::select($seq_val_sql, []);
        echo json_encode($seq_val) . PHP_EOL;
        $file_name = 'Test Batch Import.xlsx';
        $uri = 'configure_import_batch_import/Test Batch Import.xlsx';
        $method = 'POST';
        $get_parameters = [
            'directory' => 'reports',
            'task_model' => 'Report',
            'file_name' => 'Test Batch Import.xlsx',
            'fields' => [
                'event_date' => 0,
                'employers' => 1,
                'description' => 2,
                'related_organisation' => 3
            ]
        ];
        $post_parameters = [];
        $server = [
            'CONTENT_TYPE' => 'application/json',
            'REQUEST_URI' => $uri
        ];
        $request = $this->createRequest($uri, $method, $get_parameters, $server);
        $readExcelFile = new ReadExcelFile();
        $dates_array = $readExcelFile->getDateArray($file_name);
        //echo json_encode($dates_array) . PHP_EOL;
        $this->assertEquals(2, count($dates_array));
        $this->assertTrue(true);
        echo json_encode($get_parameters['file_name']) . PHP_EOL;
        echo json_encode($get_parameters['fields']) . PHP_EOL;
        $valuesArray = $readExcelFile->convertExcelToValuesArray($get_parameters['file_name'], $get_parameters['fields']);
        $this->assertEquals(6, count($valuesArray));
        //echo json_encode($valuesArray) . PHP_EOL;
        $build = $readExcelFile->buildModelsArray($request, $dates_array, new \App\Models\Report(), $valuesArray);
        //echo json_encode($build) . PHP_EOL;
        $modelsArray = $build->results;
        $this->assertEquals(2, count($modelsArray));
        $model1 = $modelsArray[0];
        echo $model1->report_id . PHP_EOL;
        $this->assertEquals(4, $model1->report_heading_id);
        $this->assertStringStartsWith('George  Harrod', $model1->description);
        $this->assertStringEndsWith('DT, IR', $model1->description);
        echo 'The first model description starts with "George  Harrod" and ends with "DT, IR"' . PHP_EOL;
        $model2 = $modelsArray[1];
        echo $model2->report_id . PHP_EOL;
        $this->assertEquals('1', $model2->report_heading_id);
        $this->assertStringStartsWith('June 25, 2016 Branch  meeting', $model2->description);
        $this->assertStringEndsWith('Budzowski', $model2->description);
        echo 'The second model description starts with "June 25, 2016 Branch  meeting" and ends with "Budzowski"' . PHP_EOL;
        echo $model2->report_heading . PHP_EOL;
        echo json_encode($model2->employers) . PHP_EOL;
        echo $model2->officers_string . PHP_EOL;
        $ids = [];
        for ($i = 0; $i < count($modelsArray); $i++) {
            $model = $modelsArray[$i];
            echo $model->report_id . PHP_EOL;
            array_push($ids, $model->report_id);
        }
        $from_id = $modelsArray[0]->report_id;
        echo $from_id . PHP_EOL;
        $delete = 'DELETE FROM reports where report_id >= ' . $from_id;
        DB::delete($delete);
        $seq_val_sql = "select setval(pg_get_serial_sequence('reports', 'report_id'), " . ($from_id - 1) . ", true);"; // reports_report_id_seq';
        //$seq_val_sql = "SELECT pg_get_serial_sequence('reports', 'report_id')";
        $seq_val = DB::select($seq_val_sql, []);
        //echo json_encode($seq_val);
        //echo json_encode($ids) . PHP_EOL;
        //$setval_sql = 'SELECT pg_catalog.setval(\'reports_report_id_seq\', ' .
        //        ($from_id - 1) .
        //        ', true);';
        //echo $setval_sql . PHP_EOL;
        //$sequence_value = DB::select($setval_sql);
        //echo json_encode($sequence_value) . PHP_EOL;
        //echo 'Failed: ' . json_encode($build->failed);
    }

    public function testSeparateHeadingFromDescription() {
        $readExcelFile = new ReadExcelFile();
        //Because of the new line  \n, wrap the test text in double quotes. 
        $description_raw = "Branch Care - Branch Meeting A load of old guff after that.\n Next line.";
        $result = $readExcelFile->separateHeadingFromDescription($description_raw);
        $this->assertEquals('Branch Care - Branch Meeting', $result->report_heading);
        $this->assertEquals('1', $result->report_heading_id);
        $this->assertEquals("A load of old guff after that.\n Next line.", $result->description);
        echo json_encode($result) . PHP_EOL;
    }

    public function testExtractOfficersFromDescription() {
        $this->assertTrue(true);
        $readExcelFile = new ReadExcelFile();
        $description_raw = "Stuff\nMore stuff.\n DT, IR, JG.";
        $result = $readExcelFile->extractOfficersFromDescription($description_raw);
        $this->assertEquals('DT', $result[0]->initials);
        $this->assertEquals('IR', $result[1]->initials);
        $this->assertEquals('JG', $result[2]->initials);
        echo $result[0]->initials . PHP_EOL;
        echo $result[1]->initials . PHP_EOL;
        echo $result[2]->initials . PHP_EOL;
    }

    public function testSetEmployers() {
        $readExcelFile = new ReadExcelFile();
        $report_id = 78;
        $employers = 'ECC,  Berlitz';
        $returned = $readExcelFile->setEmployers($report_id, $employers);
        $employers_updated_count = $returned[0]->reports_employers_update_from_csv;
        //dd($employers_returned);
        $this->assertEquals(2, $employers_updated_count);

        echo json_encode($returned);
    }

}
