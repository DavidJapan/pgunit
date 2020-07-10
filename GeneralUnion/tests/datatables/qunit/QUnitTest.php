<?php

namespace Tests\Unit;

use Tests\TestCase;
use Tests\TestHelpers;

class QUnitTest extends TestCase {

    public function testSample() {
        $route = 'qunit/sample';
        $msg = 'The route ' . $route . ' doesn\'t exist.';
        $this->assertTrue(TestHelpers::checkRoute($route), $msg);
        $className = '\App\Http\Controllers\QUnit';
        $qunit = new $className();
        $method = 'sample';
        $msg = 'The ' . $method . ' method should exist ' . $className;
        $this->assertTrue(method_exists($qunit, $method), $msg);
    }

    public function testModules() {
        $route = 'qunit/modules';
        $msg = 'The route ' . $route . ' doesn\'t exist.';
        $this->assertTrue(TestHelpers::checkRoute($route), $msg);
        $className = '\App\Http\Controllers\QUnit';
        $qunit = new $className();
        $method = 'modules';
        $msg = 'The ' . $method . ' method should exist ' . $className;
        $this->assertTrue(method_exists($qunit, $method), $msg);
    }

    public function testPrimaryKey() {
        $file_name = 'test_primary_key.blade.php';
        $full_path = resource_path('views/qunit/' . $file_name) ;
        $this->assertTrue(file_exists($full_path), 'The ' . $file_name . ' file doesn\'t exist in the qunit directory');

        $file_name = 'vendor.css';
        $full_path = public_path('css/' . $file_name) ;
        $this->assertTrue(file_exists($full_path), 'The ' . $file_name . ' file doesn\'t exist in the css directory');

        $file_name = 'app.css';
        $full_path = public_path('css/' . $file_name) ;
        $this->assertTrue(file_exists($full_path), 'The ' . $file_name . ' file doesn\'t exist in the css directory');

        $file_name = 'qunit-2.9.2.css';
        $full_path = public_path('qunit/' . $file_name) ;
        $this->assertTrue(file_exists($full_path), 'The ' . $file_name . ' file doesn\'t exist in the qunit directory');

        $route = 'qunit/test_primary_key';
        $msg = 'The route ' . $route . ' doesn\'t exist.';
        $this->assertTrue(TestHelpers::checkRoute($route), $msg);
        $className = '\App\Http\Controllers\QUnit';
        $qunit = new $className();
        $method = 'getResponse';
        $msg = 'The ' . $method . ' method should exist ' . $className;
        $this->assertTrue(method_exists($qunit, $method), $msg);
        $method = 'testPrimaryKey';
        $msg = 'The ' . $method . ' method should exist ' . $className;
        $this->assertTrue(method_exists($qunit, $method), $msg);
    }
    public function testRolesView() {
        $route = 'qunit/roles_view_get';
        $msg = 'The route ' . $route . ' doesn\'t exist.';
        $this->assertTrue(TestHelpers::checkRoute($route), $msg);
        $filepath = resource_path('views/qunit/roles_view_get.blade.php');
        //$this->assertTrue(strpos(file_get_contents($filepath), 'csrf-token') !== false);

        $route = 'qunit/roles_view_select';
        $msg = 'The route ' . $route . ' doesn\'t exist.';
        $this->assertTrue(TestHelpers::checkRoute($route), $msg);
        $filepath = resource_path('views/qunit/roles_view_select.blade.php');
        //$this->assertTrue(strpos(file_get_contents($filepath), 'csrf-token') !== false);

        $route = 'qunit/roles_view_edit';
        $msg = 'The route ' . $route . ' doesn\'t exist.';
        $this->assertTrue(TestHelpers::checkRoute($route), $msg);
        $filepath = resource_path('views/qunit/roles_view_edit.blade.php');
        //$this->assertTrue(strpos(file_get_contents($filepath), 'csrf-token') !== false);

        $route = 'qunit/roles_view_add';
        $msg = 'The route ' . $route . ' doesn\'t exist.';
        $this->assertTrue(TestHelpers::checkRoute($route), $msg);
        $filepath = resource_path('views/qunit/roles_view_add.blade.php');
        //$this->assertTrue(strpos(file_get_contents($filepath), 'csrf-token') !== false);

        $className = '\App\Http\Controllers\QUnit';
        $qunit = new $className();

        $method = 'getRoleView';
        $msg = 'The ' . $method . ' method should exist ' . $className;
        $this->assertTrue(method_exists($qunit, $method), $msg);

        $method = 'selectRoleView';
        $msg = 'The ' . $method . ' method should exist ' . $className;
        $this->assertTrue(method_exists($qunit, $method), $msg);
        
        $method = 'editRoles';
        $msg = 'The ' . $method . ' method should exist ' . $className;
        $this->assertTrue(method_exists($qunit, $method), $msg);
        
        $method = 'addRoles';
        $msg = 'The ' . $method . ' method should exist ' . $className;
        $this->assertTrue(method_exists($qunit, $method), $msg);
    }
    
}
