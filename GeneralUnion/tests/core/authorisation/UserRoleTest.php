<?php

namespace Tests\Unit;

use Tests\TestCase;
use Tests\TestHelpers;

//https://stackoverflow.com/questions/3841190/phpunit-fatal-error-handling/3842311
//These tests can cause fatal errors so run them with
//phpunit --process-isolation

class UserRoleTest extends TestCase {

    public function testDefaultUserModelDeleted() {
        $filename = app_path('User.php');
        //fwrite(STDERR, print_r($filename, TRUE));
        $this->assertFalse(file_exists($filename), 'The default User model exists in the app folder. You should delete it. ');
    }
    public function testUserModelExist() {
        $className = 'App\Models\User';
        $this->assertTrue(class_exists($className), $className . ' doesn\'t exist.');
    }

    public function testRoleModelExist() {
        $className = 'App\Models\Role';
        $this->assertTrue(class_exists($className), $className . ' doesn\'t exist.');
    }
    public function testAuthRoutes() {
        $this->assertTrue(TestHelpers::checkRoute('login'));
    }
    public function testUserRoleConfig(){
        $zizacoEntrustProvider = \Zizaco\Entrust\EntrustServiceProvider::class;
        $this->assertTrue(in_array($zizacoEntrustProvider, config('app.providers')), 'Zizaco entrust has been added as a provider.');
        $zizacoEntrustAliasKey = 'Entrust';
        $this->assertTrue(key_exists($zizacoEntrustAliasKey, config('app.aliases')), 'Zizaco entrust has been added as an alias key.');
        $zizacoEntrustAliasValue = \Zizaco\Entrust\EntrustFacade::class;
        $this->assertEquals($zizacoEntrustAliasValue, config('app.aliases')[$zizacoEntrustAliasKey], 'Zizaco entrust has been added as an alias value.');        
        $filename = config_path('entrust.php');        
        $this->assertTrue(file_exists($filename), 'The entrust config file exists in the config folder.');
    }
}
