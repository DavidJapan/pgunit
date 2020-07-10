<?php

namespace Tests\Unit;

use Tests\TestCase;
use Tests\TestHelpers;

//https://stackoverflow.com/questions/3841190/phpunit-fatal-error-handling/3842311
//These tests can cause fatal errors so run them with
//phpunit --process-isolation

class BranchesTest extends TestCase {

    public function testSectorRoutes() {
        $this->assertTrue(TestHelpers::checkRoute('sectors'));
    }

    public function testSectors() {
        $this->assertTrue(class_exists('App\Models\Sector'), 'App\Models\Sector doesn\'t exist.');
        if (class_exists('App\Models\Sector')) {
            $this->assertTrue(true, 'Ready to test attributes');
            $controller = new \App\Http\Controllers\EditableDataTableController();
            $controller->init(null, 'sectors');
            $model = $controller->model;
            $msg = 'The table property in the Sector model should be set to \'sectors\'';
            $this->assertTrue($model->getTable() === 'sectors', $msg);
            //fwrite(STDERR, print_r($sector->getTable(), TRUE)); 
        }
    }

    public function testBranchesRoutes() {
        $this->assertTrue(TestHelpers::checkRoute('branches'), 'The branches route doesn\'t exist');
    }

    public function testSelectBoxControl() {
        $class_name = 'App\AppClasses\SelectBoxControl';
        $this->assertTrue(class_exists($class_name), $class_name . ' doesn\'t exist.');
    }

    public function testSectorsLookupTable() {
        $class_name = 'App\AppClasses\LookupTables\LookupTable';
        $this->assertTrue(class_exists($class_name), $class_name . ' doesn\'t exist.');
        if (class_exists($class_name)) {
            $class_name = 'App\AppClasses\LookupTables\Sectors';
            $this->assertTrue(class_exists($class_name), $class_name . ' doesn\'t exist.');
        }
    }

    public function testBranches() {
        $class_name = 'App\Models\Branch';
        $this->assertTrue(class_exists($class_name), $class_name . ' doesn\'t exist.');
        if (class_exists($class_name)) {
            $this->assertTrue(true, 'Ready to test attributes');
            $controller = new \App\Http\Controllers\EditableDataTableController();
            $controller->init(null, 'branches');
            $model = $controller->model;
            $msg = 'The table property in the Branch model should be set to \'branches\'';
            $this->assertTrue($model->getTable() === 'branches', $msg);
            //fwrite(STDERR, print_r($sector->getTable(), TRUE)); 
        }
    }

    public function testBranchMenuExists() {
        //Great starting point:
        //https://medium.com/yish/how-to-mock-authentication-user-on-unit-test-in-laravel-1441d491d82c
        $userModel = new \App\Models\User;
        $user = $userModel->find(1);
        $this->be($user);
        $navigation = new \App\Http\Controllers\Navigation();
        $menu = $navigation->getMenu()['menu'];
        $items = $menu['items'];
        if (count($items) > 2) {
            $branch_administration = $items[5];
            $text = $branch_administration['text'];
            //fwrite(STDERR, print_r($branch_administration['text'], TRUE));            
            $this->assertEquals('Branches', $text, 'The Branches menu item is in the menu');
        } else {
            $this->assertTrue(false, 'Can\'t find the Branches item in the menu');
        }
    }

}
