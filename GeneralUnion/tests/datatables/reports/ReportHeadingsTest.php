<?php

namespace Tests\Unit;

use Tests\TestCase;
use Tests\TestHelpers;

//https://stackoverflow.com/questions/3841190/phpunit-fatal-error-handling/3842311
//These tests can cause fatal errors so run them with
//phpunit --process-isolation

class ReportHeadingsTest extends TestCase {

    public function testReportHeadingsRoutes() {
        $this->assertTrue(TestHelpers::checkRoute('report_headings'));
    }
    public function testReportHeadingsConfig(){
        $app_name = config('app.name');
        $this->assertEquals($app_name, 'datatables', 'The app name is not datatables');   
        $this->assertArrayHasKey('report_headings', config($app_name . '.models'), 'The models array in the datatables config file doesn\'t have\'report_headings\'');
        if(array_key_exists('report_headings', config($app_name . '.models'))){
            $this->assertEquals(config($app_name . '.models.report_headings'), 'ReportHeading', 'The URL report_headings doesn\'t point to the model ReportHeading');
        }
        //fwrite(STDERR, print_r(config($app_name . '.models'), TRUE));
    }
    public function testReportHeadings() {
        $this->assertTrue(class_exists('App\Models\ReportHeading'), 'App\Models\ReportHeading doesn\'t exist.');
        if (class_exists('App\Models\ReportHeading')) {
            $this->assertTrue(true, 'Ready to test attributes');
            $controller = new \App\Http\Controllers\EditableDataTableController();
            $controller->init(null, 'report_headings');
            $model = $controller->model;
            $msg = 'The table property in the ReportHeading model should be set to \'report_headings\'';
            $this->assertTrue($model->getTable() === 'report_headings', $msg);
            //fwrite(STDERR, print_r($sector->getTable(), TRUE)); 
        }
    }
    public function testReportHeadingsMenuExists() {
        //Great starting point:
        //https://medium.com/yish/hown-to-mock-authentication-user-on-unit-test-in-laravel-1441d491d82c
        $userModel = new \App\Models\User;
        $user = $userModel->find(1);
        $this->be($user);
        $navigation = new \App\Http\Controllers\Navigation();
        $menu = $navigation->getMenu()['menu'];
        $items = $menu['items'];
        $reports_administration = $items[7];
        $report_headings_item = $reports_administration['items'][1];
        //fwrite(STDERR, print_r($employers_item, TRUE));            
        $this->assertEquals('Reports Headings', $report_headings_item['text'], 'The Reports Headings menu item is in the menu');
    }

}
