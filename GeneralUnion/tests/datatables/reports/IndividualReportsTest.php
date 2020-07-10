<?php

namespace Tests\Unit;

use Tests\TestCase;
use Tests\TestHelpers;
use Auth;

class IndividualReportsTest extends TestCase {

    public function testIndividualReportsRoutes() {
        $userModel = new \App\Models\User;
        $user = $userModel->find(1);
        $this->be($user);
        $this->assertTrue(TestHelpers::checkRoute('individual_reports'), 'The individual_reports route doesn\'t exist.');
    }

    public function testIndividualReportMenuExists() {
        //Great starting point:
        //https://medium.com/yish/how-to-mock-authentication-user-on-unit-test-in-laravel-1441d491d82c
        $userModel = new \App\Models\User;
        $user = $userModel->find(1);
        $this->be($user);
        $navigation = new \App\Http\Controllers\Navigation();
        $menu = $navigation->getMenu()['menu'];
        $items = $menu['items'];
        if (count($items) > 2) {
            $individual_reports = $items[6];
            $text = $individual_reports['text'];
            //fwrite(STDERR, print_r(json_encode($menu), TRUE));
            $this->assertEquals('Reports Writer', $text, 'The Reports Writer menu item is in the menu');
        } else {
            $this->assertTrue(false, 'Can\'t find the Reports Writer item in the menu');
      }
      }

    public function testIndividualReportModel() {
            $userModel = new \App\Models\User;
            $user = $userModel->find(1);
            $this->be($user);
        $className = 'App\Models\IndividualReport';
        //fwrite(STDERR, print_r($className::$all_function, TRUE));
        $this->assertTrue(class_exists($className), $className . ' doesn\'t exist.');
        if (class_exists($className)) {
            $this->assertTrue(true, 'Ready to test attributes');
            $controller = new \App\Http\Controllers\EditableDataTableController();
            $controller->init(null, 'individual_reports');
            $model = $controller->model;
            $msg = 'The table property in the IndividualReport model should be set to \'reports\'';
            $this->assertTrue($model->getTable() === 'reports', $msg);
            $msg = 'The timestamps property in the IndividualReport model should be set to true';
            $this->assertTrue($model->timestamps === true, $msg);
            $all_function_name = 'reports_all_with_headings_for_user_get';
            $msg = 'The all_function static property of the IndividualReport model should be set to \'' . $all_function_name . '\'';
            //You need the dollar sign for static properties.
            $this->assertTrue($className::$all_function === $all_function_name, $msg);
            $msg = 'The primaryKey property in the IndividualReport model should be set to \'report_id\'';
            $this->assertTrue($model->getKeyName() === 'report_id', $msg);
            $msg = 'The updateEmployers method should exist ' . $className;
            $this->assertTrue(method_exists($model, 'updateEmployers'), $msg);
            $msg = 'The updateOfficers method should exist ' . $className;
            $this->assertTrue(method_exists($model, 'updateOfficers'), $msg);
            $msg = 'The individual_report_scripts property should exist ' . $className;
            $this->assertTrue(property_exists($model, 'individual_report_scripts'), $msg);
            $this->assertTrue(count($model->scripts()) === 8, 'There are ' . count($model->scripts()) . ' scripts.');
            $this->assertTrue(count($model->getLookupTables()) === 3, 'There are 3 lookup tables.');
        }
    }
    public function testIndividualReportsLookupTables() {
        $className = 'App\AppClasses\LookupTables\LookupTable';
        $this->assertTrue(class_exists($className), $className . ' doesn\'t exist.');
        if (class_exists($className)) {
            $className = 'App\AppClasses\LookupTables\ReportHeadings';
            $this->assertTrue(class_exists($className), $className . ' doesn\'t exist.');
            $className = 'App\AppClasses\LookupTables\Officers';
            $this->assertTrue(class_exists($className), $className . ' doesn\'t exist.');
            $className = 'App\AppClasses\LookupTables\CurrentEmployers';
            $this->assertTrue(class_exists($className), $className . ' doesn\'t exist.');
        }
    }
    public function testIndividualReportsController() {
        $className = 'App\Http\Controllers\IndividualReportsController';
        $this->assertTrue(class_exists($className), $className . ' doesn\'t exist.');
        if (class_exists($className)) {
            $msg = 'The getAvailableEmployers method should exist ' . $className;
            $this->assertTrue(method_exists($className, 'getAvailableEmployers'), $msg);
            $msg = 'The updateEmployers method should exist ' . $className;
            $this->assertTrue(method_exists($className, 'updateEmployers'), $msg);
            $msg = 'The updateOfficers method should exist ' . $className;
            $this->assertTrue(method_exists($className, 'updateOfficers'), $msg);

        }
    }     
}
