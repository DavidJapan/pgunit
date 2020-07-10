<?php

namespace Tests\Unit;

use Tests\TestCase;
use Tests\TestHelpers;

class NavigationTest extends TestCase {

    public function testNavigationRoutes() {
        $this->assertTrue(TestHelpers::checkRoute("navigation/menu"), 'The route navigation/menu doesn\'t exist.');
    }
    public function testNavigationExists() {
        $this->assertTrue(class_exists('App\Http\Controllers\Navigation'), 'Navigation doesn\'t exist.');
        if (class_exists('App\Http\Controllers\Navigation')) {
            $this->assertTrue(true, 'Ready to test for methods');
            $navigation = new \App\Http\Controllers\Navigation();
            $this->assertTrue(method_exists($navigation, 'getMenu'), 'The getMenu method does not exist');
        }
    }

    public function testEntrustConfigExists() {
        $filename = config_path('/entrust.php');
        $this->assertTrue(file_exists($filename), $filename . ' doesn\'t exist. Zicaco Entrust needs this configuration file.');
    }

    public function testUserModelExistsAndUsesTheUsersTable() {
        $this->assertTrue(true);
        $this->assertTrue(class_exists('App\Models\User'), 'App\Models\User doesn\'t exist.');
        if (class_exists('App\Models\User')) {
            $this->assertTrue(true, 'Ready to test attributes');

            $user = new \App\Models\User();
            $msg = 'The table property in the User model should be set to \'users\'';
            $this->assertAttributeSame('users', 'table',  $user, $msg);
        }
    }
    public function testRoleModelExistsAndUsesTheRolesTable() {
        $this->assertTrue(true);
        $this->assertTrue(class_exists('App\Models\Role'), 'App\Models\Role doesn\'t exist.');
        if (class_exists('App\Models\Role')) {
            $this->assertTrue(true, 'Ready to test attributes');

            $role = new \App\Models\Role();
            $msg = 'The table property in the Role model should be set to \'roles\'';
            //print_r($role->getTable());
            $this->assertSame('roles', $role->getTable(), $msg);
        }
    }

    public function testAuthorisedExists() {
        $dirname = resource_path('views/authorised');
        $this->assertTrue(file_exists($dirname), $dirname . ' doesn\'t exist');
    }

    public function testCrudJs() {
        $dirname = public_path('js/crud');
        $this->assertTrue(file_exists($dirname), $dirname . ' doesn\'t exist');
        if (file_exists($dirname)) {
            $filename = $dirname . '/leftmenu_ajaxhtml.js';
            $this->assertTrue(file_exists($filename), $filename . ' doesn\'t exist');
            $filename = $dirname . '/get_scripts.js';
            $this->assertTrue(file_exists($filename), $filename . ' doesn\'t exist');
            $filename = $dirname . '/data_table_factory.js';
            $this->assertTrue(file_exists($filename), $filename . ' doesn\'t exist');
            $filename = $dirname . '/error_handler.js';
            $this->assertTrue(file_exists($filename), $filename . ' doesn\'t exist');
            $filename = $dirname . '/validation_rules.js';
            $this->assertTrue(file_exists($filename), $filename . ' doesn\'t exist');
            $filename = $dirname . '/data_table_factory_templates.js';
            $this->assertTrue(file_exists($filename), $filename . ' doesn\'t exist');
            $filename = $dirname . '/primary_key.js';
            $this->assertTrue(file_exists($filename), $filename . ' doesn\'t exist');
            $filename = $dirname . '/data_table_factory_extend.js';
            $this->assertTrue(file_exists($filename), $filename . ' doesn\'t exist');
            $filename = $dirname . '/template_binding_context.js';
            $this->assertTrue(file_exists($filename), $filename . ' doesn\'t exist');
            $filename = $dirname . '/view_model_standard.js';
            $this->assertTrue(file_exists($filename), $filename . ' doesn\'t exist');
            $filename = $dirname . '/new_item.js';
            $this->assertTrue(file_exists($filename), $filename . ' doesn\'t exist');
            $filename = $dirname . '/add.js';
            $this->assertTrue(file_exists($filename), $filename . ' doesn\'t exist');
            $filename = $dirname . '/edit.js';
            $this->assertTrue(file_exists($filename), $filename . ' doesn\'t exist');
            $filename = $dirname . '/selected_item.js';
            $this->assertTrue(file_exists($filename), $filename . ' doesn\'t exist');
            $filename = $dirname . '/selected_items.js';
            $this->assertTrue(file_exists($filename), $filename . ' doesn\'t exist');
            $filename = $dirname . '/view_model_edit_users.js';
            $this->assertTrue(file_exists($filename), $filename . ' doesn\'t exist');
        }
    }
    
}
