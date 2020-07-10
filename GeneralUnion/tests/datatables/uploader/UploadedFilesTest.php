<?php

namespace Tests\Unit;

use Tests\TestCase;
use Tests\TestHelpers;

//https://stackoverflow.com/questions/3841190/phpunit-fatal-error-handling/3842311
//These tests can cause fatal errors so run them with
//phpunit --process-isolation

class UploadedFilesTest extends TestCase {

    public function testUploadedFilesRoutes() {
        $this->assertTrue(TestHelpers::checkRoute('uploaded_files/view'));
        $this->assertTrue(TestHelpers::checkRoute('uploaded_files'));
    }

    public function testUploadedFilesConfig() {
        $app_name = config('app.name');
        $this->assertEquals($app_name, 'datatables', 'The app name is not datatables');
        $this->assertArrayHasKey('PATH_TO_EXCEL', config($app_name),
                'The models array in the datatables config file doesn\'t have\'PATH_TO_EXCEL\'');
        $this->assertArrayHasKey('PATH_TO_PDF', config($app_name),
                'The models array in the datatables config file doesn\'t have\'PATH_TO_PDF\'');
        $this->assertArrayHasKey('uploaded_files', config($app_name . '.models'),
                'The models array in the datatables config file doesn\'t have\'uploaded_files\'');
        if (array_key_exists('uploaded_files', config($app_name . '.models'))) {
            $this->assertEquals(config($app_name . '.models.uploaded_files'), 'UploadedFile',
                    'The URL uploaded_files doesn\'t point to the model UploadedFile');
            $this->assertEquals(config($app_name . '.PATH_TO_EXCEL'), 'exceldata',
                    'The Excel files are stored in the exceldata directory');
            $filename = storage_path(config($app_name . '.PATH_TO_EXCEL'));
            $this->assertTrue(file_exists($filename), 'The exceldata directory doesn\'t exist in the storage directory');

            $this->assertEquals(config($app_name . '.PATH_TO_PDF'), 'pdfdata',
                    'The PDF files are stored in the pdfdata directory');
            $filename = storage_path(config($app_name . '.PATH_TO_PDF'));
            $this->assertTrue(file_exists($filename), 'The pdfdata directory doesn\'t exist in the storage directory');
        }
    }

    public function testUploadedFile() {
        $this->assertTrue(class_exists('App\Models\UploadedFile'), 'App\Models\UploadedFile doesn\'t exist.');
        if (class_exists('App\Models\UploadedFile')) {
            $this->assertTrue(true, 'Ready to test attributes');
            $controller = new \App\Http\Controllers\UploadedFilesController();
            $controller->init(null, 'uploaded_files');
            $model = $controller->model;
            $msg = 'The table property in the UploadedFile model should be \'files\'';
            //However, note that ExcelFile doesn't map to a table in the database.
            $this->assertTrue($model->getTable() === 'files', $msg);
            //fwrite(STDERR, print_r($model->getTable(), TRUE));
        }
    }

    public function testUploadedFilesController() {

        $className = 'App\Http\Controllers\UploadedFilesController';
        $this->assertTrue(class_exists($className), $className . ' doesn\'t exist.');
        if (class_exists($className)) {
            $msg = 'The store method should exist ' . $className;
            $this->assertTrue(method_exists($className, 'store'), $msg);
            /*
              $msg = 'The key method should exist ' . $className;
              $this->assertTrue(method_exists($className, 'key'), $msg);
              $msg = 'The update method should exist ' . $className;
              $this->assertTrue(method_exists($className, 'update'), $msg);
              $msg = 'The delete method should exist ' . $className;
              $this->assertTrue(method_exists($className, 'delete'), $msg);
             */
        }
    }

    public function testUploadedFilesMenuExists() {
        //Great starting point:
        //https://medium.com/yish/how-to-mock-authentication-user-on-unit-test-in-laravel-1441d491d82c
        $userModel = new \App\Models\User;
        $user = $userModel->find(1);
        $this->be($user);
        $navigation = new \App\Http\Controllers\Navigation();
        $menu = $navigation->getMenu()['menu'];
        $items = $menu['items'];
        $uploaded_files = $items[4];
        $text = $uploaded_files['text'];
        //fwrite(STDERR, print_r($uploaded_files['text'], TRUE));

        $this->assertEquals('Uploader', $text, 'The Uploader menu item is in the menu');
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

    public function testUploadedFilesViews() {
        $dirname = public_path('uploader/js');
        $filename = $dirname . '/view_model_uploader.js';
        $this->assertTrue(file_exists($filename), $filename . ' doesn\'t exist');
        $filename = $dirname . '/selected_file.js';
        $this->assertTrue(file_exists($filename), $filename . ' doesn\'t exist');
        $dirname = resource_path('views/uploader');
        $this->assertTrue(file_exists($dirname), 'The uploader directory doesn\'t exist in the views directory');
        if (file_exists($dirname)) {
            $filename = resource_path('views/uploader/uploaded_files.blade.php');
            $this->assertTrue(file_exists($filename), 'The uploaded_files file doesn\'t exist in the uploader directory');
            $set_string = strpos(file_get_contents($filename), "viewModel.set(serverMap);");
            //fwrite(STDERR, print_r($schema_from_env, TRUE));
            $this->assertTrue($set_string !== false, 'The uploaded_files file calls the set method,  passing the serverMap');
        }
    }

    public function testUploadedFilesJsCss() {
        $dirname = public_path('uploader');
        $this->assertTrue(file_exists($dirname), $dirname . ' exists on the system.');
        if (file_exists($dirname)) {
            $filename = $dirname . '/js/view_model_uploader.js';
            $this->assertTrue(file_exists($filename), $filename . ' doesn\'t exist');
            $filename = $dirname . '/js/jquery.dm-uploader.js';
            $this->assertTrue(file_exists($filename), $filename . ' doesn\'t exist');
            $filename = $dirname . '/js/selected_file.js';
            $this->assertTrue(file_exists($filename), $filename . ' doesn\'t exist');
            $filename = $dirname . '/css/jquery.dm-uploader.min.css';
            $this->assertTrue(file_exists($filename), $filename . ' doesn\'t exist');
            $filename = $dirname . '/css/styles.css';
            $this->assertTrue(file_exists($filename), $filename . ' doesn\'t exist');
        }
    }

}
