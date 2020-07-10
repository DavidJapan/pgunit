<?php

namespace Tests\Unit;

use Tests\TestCase;
use Tests\TestHelpers;

//https://stackoverflow.com/questions/3841190/phpunit-fatal-error-handling/3842311
//These tests can cause fatal errors so run them with
//phpunit --process-isolation

class EmployersTest extends TestCase {

    public function testEmployerRoutes() {
        $this->assertTrue(TestHelpers::checkRoute('employers'));
    }
    public function testEmployerConfig(){
        $app_name = config('app.name');
        $this->assertEquals($app_name, 'datatables', 'The app name is not datatables');   
        $this->assertArrayHasKey('employers', config($app_name . '.models'), 'The models array in the datatables config file doesn\'t have\'employers\'');
        if(array_key_exists('employers', config($app_name . '.models'))){
            $this->assertEquals(config($app_name . '.models.employers'), 'Employer', 'The URL employers doesn\'t point to the model Employer');
        }
        //fwrite(STDERR, print_r(config($app_name . '.models'), TRUE));
    }
    public function testEmployers() {
        $this->assertTrue(class_exists('App\Models\Employer'), 'App\Models\Employer doesn\'t exist.');
        if (class_exists('App\Models\Employer')) {
            $this->assertTrue(true, 'Ready to test attributes');
            $controller = new \App\Http\Controllers\EditableDataTableController();
            $controller->init(null, 'employers');
            $model = $controller->model;
            $msg = 'The table property in the Employer model should be set to \'employers\'';
            $this->assertTrue($model->getTable() === 'employers', $msg);
            //fwrite(STDERR, print_r($sector->getTable(), TRUE)); 
        }
    }
    public function testEmployerMenuExists() {
        //Great starting point:
        //https://medium.com/yish/how-to-mock-authentication-user-on-unit-test-in-laravel-1441d491d82c
        $userModel = new \App\Models\User;
        $user = $userModel->find(1);
        $this->be($user);
        $navigation = new \App\Http\Controllers\Navigation();
        $menu = $navigation->getMenu()['menu'];
        $items = $menu['items'];
        $reports_administration = $items[7];
        $employers_item = $reports_administration['items'][0];
        //fwrite(STDERR, print_r($employers_item, TRUE));            
        $this->assertEquals('Employers/Org/Event', $employers_item['text'], 'The Employers menu item is in the menu');
    }

}
