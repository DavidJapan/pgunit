<?php

namespace Tests\Unit;

use Tests\TestCase;
use Tests\TestHelpers;

class ReportsTest extends TestCase {

    public function testReportsRoutes() {
        $this->assertTrue(TestHelpers::checkRoute('reports'), 'The reports route doesn\'t exist.');
    }

    public function testCurrentEmployersLookupTable() {
        $class_name = 'App\AppClasses\LookupTables\CurrentEmployers';
        $this->assertTrue(class_exists($class_name), $class_name . ' doesn\'t exist.');
    }

    public function testReportHeadingsLookupTable() {
        $class_name = 'App\AppClasses\LookupTables\ReportHeadings';
        $this->assertTrue(class_exists($class_name), $class_name . ' doesn\'t exist.');
    }

    public function testOfficersLookupTable() {
        $class_name = 'App\AppClasses\LookupTables\Officers';
        $this->assertTrue(class_exists($class_name), $class_name . ' doesn\'t exist.');
    }

    public function testReportsController() {
        $class_name = 'App\Http\Controllers\ReportsController';
        $this->assertTrue(class_exists($class_name), $class_name . ' doesn\'t exist.');
    }

    public function testReports() {
        $class_name = 'App\Models\Report';
        $this->assertTrue(class_exists($class_name), $class_name . ' doesn\'t exist.');
        if (class_exists($class_name)) {
            //Great starting point:
            //https://medium.com/yish/how-to-mock-authentication-user-on-unit-test-in-laravel-1441d491d82c
            $userModel = new \App\Models\User;
            $user = $userModel->find(1);
            $this->be($user);
            $this->assertTrue(true, 'Ready to test attributes');
            $controller = new \App\Http\Controllers\EditableDataTableController();
            $controller->init(null, 'reports');
            $model = $controller->model;
            $msg = 'The table property in the Report model should be set to \'reports\'';
            $this->assertTrue($model->getTable() === 'reports', $msg);
            //fwrite(STDERR, print_r($sector->getTable(), TRUE)); 
            $className = 'App\Models\Report';
            $msg = 'The getAvailableEmployers method should exist in ' . $className;
            $this->assertTrue(method_exists($model, 'getAvailableEmployers'), $msg);
            $msg = 'The getAvailableOfficers method should exist in ' . $className;
            $this->assertTrue(method_exists($model, 'getAvailableOfficers'), $msg);
            $msg = 'The updateEmployers method should exist in ' . $className;
            $this->assertTrue(method_exists($model, 'updateEmployers'), $msg);
            $msg = 'The updateOfficers method should exist in ' . $className;
            $this->assertTrue(method_exists($model, 'updateOfficers'), $msg);
            $msg = 'The handleSuccessfulUpdate method should exist in ' . $className;
            $this->assertTrue(method_exists($model, 'handleSuccessfulUpdate'), $msg);
            $msg = 'The validateDate static method should exist in ' . $className;
            $this->assertTrue(method_exists($className, 'validateDate'), $msg);
            $msg = 'The getSelectFilteredByColumns method should exist in ' . $className;
            $this->assertTrue(method_exists($model, 'getSelectFilteredByColumns'), $msg);
            $msg = 'The getRecordsFilteredByColumnsCount method should exist in ' . $className;
            $this->assertTrue(method_exists($model, 'getRecordsFilteredByColumnsCount'), $msg);
            $msg = 'The getDataFilteredByColumns method should exist in ' . $className;
            $this->assertTrue(method_exists($model, 'getDataFilteredByColumns'), $msg);
            $msg = 'The deleteReport method should exist in ' . $className;
            $this->assertTrue(method_exists($model, 'deleteReport'), $msg);    
            $msg = 'The getFilteredData method should exist in ' . $className;
            $this->assertTrue(method_exists($model, 'getFilteredData'), $msg);    
            $msg = 'The one_from_new_function method should exist in ' . $className;
            $this->assertTrue(method_exists($model, 'one_from_new_function'), $msg);    
            $msg = 'The new_one method should exist in ' . $className;
            $this->assertTrue(method_exists($model, 'new_one'), $msg);    
            
        }
    }

    public function testReportAuthorisedRolesExist() {
        $this->assertTrue(array_key_exists('ROLE_REPORT_EDITOR', config('datatables')), 'The ROLE_REPORT_EDITOR key doesn\'t exist in the datatables.php config file.');
    }

    public function testReportMenuExists() {
        //Great starting point:
        //https://medium.com/yish/how-to-mock-authentication-user-on-unit-test-in-laravel-1441d491d82c
        $userModel = new \App\Models\User;
        $user = $userModel->find(1);
        $this->be($user);
        $navigation = new \App\Http\Controllers\Navigation();
        $menu = $navigation->getMenu()['menu'];
        $items = $menu['items'];
        if (count($items) > 2) {
            $report_administration = $items[7];
            $text = $report_administration['text'];
            //fwrite(STDERR, print_r($branch_administration['text'], TRUE));            
            $this->assertEquals('Reports Editor', $text, 'The Reports Editor menu item is in the menu');
        } else {
            $this->assertTrue(false, 'Can\'t find the Reports Editor item in the menu');
        }
    }

    public function testReportViews() {
        $dirname = public_path('js/crud');
        $filename = $dirname . '/reports.js';
        //fwrite(STDERR, print_r($filename, TRUE));            
        $this->assertTrue(file_exists($filename), $filename . ' doesn\'t exist');
        $filename = $dirname . '/view_model_reports.js';
        $this->assertTrue(file_exists($filename), $filename . ' doesn\'t exist');
        $filename = $dirname . '/knockout-datetimepicker.js';
        $this->assertTrue(file_exists($filename), $filename . ' doesn\'t exist');
        $filename = $dirname . '/knockout-datetimepicker-standalone.js';
        $this->assertTrue(file_exists($filename), $filename . ' doesn\'t exist');

        $filename = resource_path('views/reports');
        $this->assertTrue(file_exists($filename), 'The reports directory doesn\'t exist in the views directory');
        if (file_exists($filename)) {
            $filename = resource_path('views/reports/reports.blade.php');
            $this->assertTrue(file_exists($filename), 'The reports blade doesn\'t exist in the reports directory');
        }
    }

}
