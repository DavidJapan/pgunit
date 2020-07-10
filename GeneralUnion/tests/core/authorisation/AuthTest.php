<?php

namespace Tests\Unit;

use Tests\TestCase;
use Tests\TestHelpers;

//https://stackoverflow.com/questions/3841190/phpunit-fatal-error-handling/3842311
//These tests can cause fatal errors so run them with
//phpunit --process-isolation

class AuthTest extends TestCase {

    public function testIndexView() {
        $filename = resource_path('views/authorised/index.blade.php');
        $this->assertTrue(file_exists($filename), $filename . ' doesn\'t exist.');
    }

    public function testAuthlades() {
        $dirname = resource_path('views/auth');
        $this->assertTrue(file_exists($dirname), 'The auth directory doesn\'t exist in the views directory');
        if (file_exists($dirname)) {
            $filename = resource_path('views/auth/login.blade.php');
            $this->assertTrue(file_exists($filename), 'The login blade file doesn\'t exist in the auth directory');
            $filename = resource_path('views/auth/passwords/email.blade.php');
            $this->assertTrue(file_exists($filename), 'The email blade file doesn\'t exist in the auth directory');
            $filename = resource_path('views/auth/passwords/reset.blade.php');
            $this->assertTrue(file_exists($filename), 'The reset blade file doesn\'t exist in the auth directory');
        }
    }

}
