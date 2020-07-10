<?php

namespace Tests\Unit;

use Tests\TestCase;
use Tests\TestHelpers;

class CollectiveAgreementsTest extends TestCase {

    public function testCollectiveAgreementsRoutes() {
        $this->assertTrue(TestHelpers::checkRoute('collective_agreements'), 'The collective_agreements route doesn\'t exist.');
    }

    public function testCollectiveAgreementConfig() {
        $app_name = config('app.name');
        $this->assertEquals($app_name, 'datatables', 'The app name is not datatables');
        $this->assertArrayHasKey('collective_agreements', config($app_name . '.models'),
                'The models array in the datatables config file doesn\'t have\'collective_agreements\'');
        if (array_key_exists('collective_agreements', config($app_name . '.models'))) {
            $this->assertEquals(config($app_name . '.models.collective_agreements'), 'CollectiveAgreement',
                    'The URL collective_agreements doesn\'t point to the model CollectiveAgreement');
        }
        //fwrite(STDERR, print_r(config($app_name . '.models'), TRUE));
    }

    public function testCurrentEmployersLookupTable() {
        $class_name = 'App\AppClasses\LookupTables\CurrentEmployers';
        $this->assertTrue(class_exists($class_name), $class_name . ' doesn\'t exist.');
    }

    public function testCollectiveAgreements() {
        $class_name = 'App\Models\CollectiveAgreement';
        $this->assertTrue(class_exists($class_name), $class_name . ' doesn\'t exist.');
        if (class_exists($class_name)) {
            //Great starting point:
            //https://medium.com/yish/how-to-mock-authentication-user-on-unit-test-in-laravel-1441d491d82c
            $userModel = new \App\Models\User;
            $user = $userModel->find(1);
            $this->be($user);
            $this->assertTrue(true, 'Ready to test attributes');
            $controller = new \App\Http\Controllers\EditableDataTableController();
            $controller->init(null, 'collective_agreements');
            $model = $controller->model;
            $msg = 'The table property in the CollectiveAgreement model should be set to \'collective_agreements\'';
            $this->assertTrue($model->getTable() === 'collective_agreements', $msg);
            //fwrite(STDERR, print_r($sector->getTable(), TRUE));
            $className = 'App\Models\CollectiveAgreement';
            //$msg = 'The getAvailableEmployers method should exist in ' . $className;
            //$this->assertTrue(method_exists($model, 'getAvailableEmployers'), $msg);
            //$msg = 'The updateEmployers method should exist in ' . $className;
            //$this->assertTrue(method_exists($model, 'updateEmployers'), $msg);
            $msg = 'The handleSuccessfulUpdate method should exist in ' . $className;
            $this->assertTrue(method_exists($model, 'handleSuccessfulUpdate'), $msg);
        }
    }

    public function testCollectiveAgreementAuthorisedRolesExist() {
        $this->assertTrue(array_key_exists('ROLE_COLLECTIVE_AGREEMENT_EDITOR', config('datatables')),
                'The ROLE_COLLECTIVE_AGREEMENT_EDITOR key doesn\'t exist in the datatables.php config file.');
    }

    public function testCollectiveAgreementMenuExists() {
        //Great starting point:
        //https://medium.com/yish/how-to-mock-authentication-user-on-unit-test-in-laravel-1441d491d82c
        $userModel = new \App\Models\User;
        $user = $userModel->find(1);
        $this->be($user);
        $navigation = new \App\Http\Controllers\Navigation();
        $menu = $navigation->getMenu()['menu'];
        $items = $menu['items'];
        if (count($items) > 2) {
            $collective_agreements = $items[8];
            $text = $collective_agreements['text'];
            //fwrite(STDERR, print_r($branch_administration['text'], TRUE));
            $this->assertEquals('Collective Agreements', $text, 'The Collective Agreements menu item is in the menu');
        } else {
            $this->assertTrue(false, 'Can\'t find the Collective Agreements item in the menu');
        }
    }

    public function testCollectiveAgreementViews() {
        $dirname = public_path('js/crud');
        $filename = $dirname . '/collective_agreements.js';
        //fwrite(STDERR, print_r($filename, TRUE));
        $this->assertTrue(file_exists($filename), $filename . ' doesn\'t exist');
        $filename = $dirname . '/view_model_collective_agreements.js';
        $this->assertTrue(file_exists($filename), $filename . ' doesn\'t exist');
        $filename = $dirname . '/knockout-datetimepicker.js';
        $this->assertTrue(file_exists($filename), $filename . ' doesn\'t exist');

        $filename = resource_path('views/collective_agreements');
        $this->assertTrue(file_exists($filename), 'The collective_agreements directory doesn\'t exist in the views directory');
        if (file_exists($filename)) {
            $filename = resource_path('views/collective_agreements/collective_agreements.blade.php');
            $this->assertTrue(file_exists($filename), 'The collective_agreements blade doesn\'t exist in the collective_agreements directory');
        }
    }

}
