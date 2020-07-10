<?php

namespace Tests\Unit;

use Tests\TestCase;
use Tests\TestHelpers;

//https://stackoverflow.com/questions/3841190/phpunit-fatal-error-handling/3842311
//These tests can cause fatal errors so run them with
//phpunit --process-isolation

class ExcelTest extends TestCase {

    public function testExcelRoutes() {
        //$this->assertTrue(TestHelpers::checkRoute('excel_files'));
        //I don't use read_excel_file alone, I only have that route with a second component.
        $this->assertTrue(TestHelpers::checkRoute('read_excel_file/view_file/{file_name}'));
        $this->assertTrue(TestHelpers::checkRoute('read_excel_file/merge'));
        
        //$this->assertTrue(TestHelpers::checkRoute('read_excel_file/get/{file_name}'));
        $this->assertTrue(TestHelpers::checkRoute('download/{filename}'));
        //$this->assertTrue(TestHelpers::checkRoute('read_excel_file/get/{file_name}'));
    }
    public function testReadExcelFileController() {
        $className = 'App\Http\Controllers\ReadExcelFile';
        $this->assertTrue(class_exists($className), $className . ' doesn\'t exist.');
        if (class_exists($className)) {
            $msg = 'The getFullPathToExcel method should exist ' . $className;
            $this->assertTrue(method_exists($className, 'getFullPathToExcel'), $msg);
            //$msg = 'The merge method should exist ' . $className;
            //$this->assertTrue(method_exists($className, 'merge'), $msg);
            $msg = 'The viewFile method should exist ' . $className;
            $this->assertTrue(method_exists($className, 'viewFile'), $msg);
            $msg = 'The getForDisplay method should exist ' . $className;
            $this->assertTrue(method_exists($className, 'getForDisplay'), $msg);
            
        }
        $className = 'Box\Spout\Reader\Common\Creator\ReaderEntityFactory';
        $this->assertTrue(class_exists($className), $className . ' doesn\'t exist.');
    }
    public function testExcelFilesJsCss() {
        $dirname = public_path('js/fileupload');
        $this->assertTrue(file_exists($dirname), $dirname . ' exists on the system.');
    /*
        if (file_exists($dirname)) {
            $filename = $dirname . '/jquery.iframe-transport.js';
            $this->assertTrue(file_exists($filename), $filename . ' doesn\'t exist');
            $filename = $dirname . '/jquery.fileupload.js';
            $this->assertTrue(file_exists($filename), $filename . ' doesn\'t exist');
            $filename = $dirname . '/jquery.fileupload-process.js';
            $this->assertTrue(file_exists($filename), $filename . ' doesn\'t exist');
            $filename = $dirname . '/jquery.fileupload-validate.js';
            $this->assertTrue(file_exists($filename), $filename . ' doesn\'t exist');
            $filename = $dirname . '/excel_file_upload.js';
            $this->assertTrue(file_exists($filename), $filename . ' doesn\'t exist');
        }
        $dirname = public_path('css/fileupload');
        $this->assertTrue(file_exists($dirname), $dirname . ' exists on the system.');
        if (file_exists($dirname)) {
            $filename = $dirname . '/bootstrap.css';
            $this->assertTrue(file_exists($filename), $filename . ' doesn\'t exist');
            $filename = $dirname . '/jquery.fileupload.css';
            $this->assertTrue(file_exists($filename), $filename . ' doesn\'t exist');
            $filename = $dirname . '/style.css';
            $this->assertTrue(file_exists($filename), $filename . ' doesn\'t exist');
        }
     * 
     */
    }
    /*
    public function testExcelFileConfig() {
        //$path = config("datatables.PATH_TO_EXCEL");
        //$_full_path = storage_path($path);
        //$full_path = str_replace("\\", "/", $_full_path);
        //fwrite(STDERR, print_r($full_path, TRUE));
        
        $app_name = config('app.name');
        $this->assertEquals($app_name, 'datatables', 'The app name is not datatables');
        $this->assertArrayHasKey('excel_files', config($app_name . '.models'),
                'The models array in the datatables config file doesn\'t have\'excel_files\'');
        if (array_key_exists('excel_files', config($app_name . '.models'))) {
            $this->assertEquals(config($app_name . '.models.excel_files'), 'ExcelFile', 'The URL excel_files doesn\'t point to the model ExcelFile');
        }
        $this->assertEquals(config($app_name . '.PATH_TO_EXCEL'), 'exceldata',
                'The Excel files are stored in the exceldata directory');
        $filename = storage_path(config($app_name . '.PATH_TO_EXCEL'));
        $this->assertTrue(file_exists($filename), 'The exceldata directory doesn\'t exist in the storage directory');

    }

    public function testExcelFile() {
        $this->assertTrue(class_exists('App\Models\ExcelFile'), 'App\Models\ExcelFile doesn\'t exist.');
        if (class_exists('App\Models\ExcelFile')) {
            $this->assertTrue(true, 'Ready to test attributes');
            $controller = new \App\Http\Controllers\EditableDataTableController();
            $controller->init(null, 'excel_files');
            $model = $controller->model;
            $msg = 'The table property in the ExcelFile model should be \'excel_files\'';
            //However, note that ExcelFile doesn't map to a table in the database.
            $this->assertTrue($model->getTable() === 'excel_files', $msg);
            //fwrite(STDERR, print_r($model->getTable(), TRUE)); 
        }
    }


    public function testExcelFilesMenuExists() {
        //Great starting point:
        //https://medium.com/yish/how-to-mock-authentication-user-on-unit-test-in-laravel-1441d491d82c
        $userModel = new \App\Models\User;
        $user = $userModel->find(1);
        $this->be($user);
        $navigation = new \App\Http\Controllers\Navigation();
        $menu = $navigation->getMenu()['menu'];
        $items = $menu['items'];
        if (count($items) > 2) {
            $excel_files = $items[3];
            $text = $excel_files['text'];
            //fwrite(STDERR, print_r($branch_administration['text'], TRUE));            
            $this->assertEquals('Excel', $text, 'The Excel menu item is in the menu');
        } else {
            $this->assertTrue(false, 'Can\'t find the Excel item in the menu');
        }
    }

    public function testIoController() {
        $className = 'App\Http\Controllers\IO';
        $this->assertTrue(class_exists($className), $className . ' doesn\'t exist.');
        if (class_exists($className)) {
            $msg = 'The fileupload method should exist ' . $className;
            $this->assertTrue(method_exists($className, 'fileupload'), $msg);
            $msg = 'The downloadFile method should exist ' . $className;
            $this->assertTrue(method_exists($className, 'downloadFile'), $msg);
        }
    }

    public function testManageExcelViews() {
        $dirname = public_path('js/crud');
        $filename = $dirname . '/view_model_excel_files.js';
        $this->assertTrue(file_exists($filename), $filename . ' doesn\'t exist');
        $filename = $dirname . '/selected_excel_file.js';
        $this->assertTrue(file_exists($filename), $filename . ' doesn\'t exist');

        $filename = resource_path('views/manage_excel');
        $this->assertTrue(file_exists($filename), 'The manage_excel directory doesn\'t exist in the views directory');
        if (file_exists($filename)) {
            $filename = resource_path('views/manage_excel/excel_files.blade.php');
            $this->assertTrue(file_exists($filename), 'The excel_files file doesn\'t exist in the manage_excel directory');
            $set_string = strpos(file_get_contents($filename), "viewModel.set(serverMap);");
            //fwrite(STDERR, print_r($schema_from_env, TRUE));            
            $this->assertTrue($set_string !== false, 'The excel_files file calls the set method,  passing the serverMap');

            $filename = resource_path('views/manage_excel/configure_import.blade.php');
            $this->assertTrue(file_exists($filename), 'The configure_import file doesn\'t exist in the manage_excel directory');
            $filename = resource_path('views/manage_excel/fileupload.php');
            $this->assertTrue(file_exists($filename), 'The fileupload file doesn\'t exist in the manage_excel directory');
            $filename = resource_path('views/manage_excel/key.php');
            $this->assertTrue(file_exists($filename), 'The key file doesn\'t exist in the manage_excel directory');
        }
    }

     * 
     */

}
