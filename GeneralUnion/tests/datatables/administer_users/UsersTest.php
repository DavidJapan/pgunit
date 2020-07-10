<?php

namespace Tests\Unit;

use Tests\TestCase;
use Tests\TestHelpers;
use App\Models\AdministerRole;

//https://stackoverflow.com/questions/3841190/phpunit-fatal-error-handling/3842311
//These tests can cause fatal errors so run them with
//phpunit --process-isolation

class UsersTest extends TestCase {

    public function testAdministerUserClassesExist() {
        $className = 'App\Http\Controllers\AdministerUsers';
        $this->assertTrue(class_exists($className), $className . ' doesn\'t exist.');
        if (class_exists($className)) {
            $this->assertTrue(true, 'Ready to test attributes');
            $administerUsers = new \App\Http\Controllers\AdministerUsers();
            $msg = 'The AuthRouteAPI method should exist ' . $className;
            $this->assertTrue(method_exists($administerUsers, 'AuthRouteAPI'), $msg);
            $msg = 'The attachRole method should exist ' . $className;
            $this->assertTrue(method_exists($administerUsers, 'attachRole'), $msg);
            $msg = 'The detachRole method should exist ' . $className;
            $this->assertTrue(method_exists($administerUsers, 'detachRole'), $msg);
            $msg = 'The updatePassword method should exist ' . $className;
            $this->assertTrue(method_exists($administerUsers, 'updatePassword'), $msg);
        }
        $className = 'App\Models\AdministerUser';
        $this->assertTrue(class_exists($className), $className . ' doesn\'t exist.');
    }

    public function testAdministerUserRoutes() {
        $this->assertTrue(TestHelpers::checkRoute('administer_users'));
        $this->assertTrue(TestHelpers::checkRoute('administer_users/view'));
    }

    public function testAdministerUserViews() {
        $dirname = public_path('js/crud');
        $filename = $dirname . '/user.js';
        $this->assertTrue(file_exists($filename), $filename . ' doesn\'t exist');
        $filename = resource_path('views/users');
        $this->assertTrue(file_exists($filename), 'The users directory doesn\'t exist in the views directory');
        if (file_exists($filename)) {
            $filename = resource_path('views/users/editusers.blade.php');
            $this->assertTrue(file_exists($filename), 'The editusers blade doesn\'t exist in the users directory');
            $filename = resource_path('views/users/childviews');
            $this->assertTrue(file_exists($filename), 'The childviews directory doesn\'t exist in the users directory');
            if (file_exists($filename)) {
                $filename = resource_path('views/users/childviews/add_user.blade.php');
                $this->assertTrue(file_exists($filename), 'The editusers blade doesn\'t exist in the childviews directory');
                $filename = resource_path('views/users/childviews/user_editor.blade.php');
                $this->assertTrue(file_exists($filename), 'The user_editor blade doesn\'t exist in the childviews directory');
                $filename = resource_path('views/users/childviews/change_password.blade.php');
                $this->assertTrue(file_exists($filename), 'The change_password blade doesn\'t exist in the childviews directory');
                $filename = resource_path('views/users/childviews/roles_table.blade.php');
                $this->assertTrue(file_exists($filename), 'The roles_table file doesn\'t exist in the childviews directory');
            }
        }
    }
    public function testAdministerRoleClassesExist() {
        $className = 'App\Models\AdministerRole';
        $this->assertTrue(class_exists($className), $className . ' doesn\'t exist.');
        if (class_exists($className)) {
            $this->assertTrue(true, 'Ready to test attributes');
            $administerUsers = new \App\Http\Controllers\AdministerUsers();
            $msg = 'The AuthRouteAPI method should exist ' . $className;
            $this->assertTrue(method_exists($administerUsers, 'AuthRouteAPI'), $msg);
            $msg = 'The attachRole method should exist ' . $className;
            $this->assertTrue(method_exists($administerUsers, 'attachRole'), $msg);
            $msg = 'The detachRole method should exist ' . $className;
            $this->assertTrue(method_exists($administerUsers, 'detachRole'), $msg);
            $msg = 'The updatePassword method should exist ' . $className;
            $this->assertTrue(method_exists($administerUsers, 'updatePassword'), $msg);
        }
    }
    public function testConfigRolesMatchRolesInDatabase(){
        $configRoles = [];
        foreach(config('datatables') as $key=>$item){
            if(substr($key, 0, 4) === 'ROLE'){
               array_push($configRoles, $item);
            }
        }
        sort($configRoles);
        $roles = AdministerRole::all();
        $dbRoles = [];
        foreach($roles as $role){
            array_push($dbRoles, $role->name);
        }
        sort($dbRoles);
        $this->assertEquals($configRoles, $dbRoles);
//        fwrite(STDERR, print_r(json_encode($configRoles), TRUE));    
//        fwrite(STDERR, print_r(json_encode($dbRoles), TRUE));    

    }

}
