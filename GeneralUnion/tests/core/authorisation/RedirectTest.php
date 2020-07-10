<?php

namespace Tests\Unit;

use Tests\TestCase;

class RedirectTest extends TestCase {

    public function testLoginController() {
        $value = '/authorised';
        $name = 'LoginController';
        $controller = '\\App\\Http\\Controllers\\Auth\\' . $name;
        $msg = 'The redirectTo property in the  should be set to \' ' . $value . '\'';
        $this->assertAttributeSame($value, 'redirectTo', new $controller(), $msg);

        $name = 'RegisterController';
        $controller = '\\App\\Http\\Controllers\\Auth\\' . $name;
        $msg = 'The redirectTo property in the  should be set to \' ' . $value . '\'';
        $this->assertAttributeSame($value, 'redirectTo', new $controller(), $msg);

        $name = 'ResetPasswordController';
        $controller = '\\App\\Http\\Controllers\\Auth\\' . $name;
        $msg = 'The redirectTo property in the  should be set to \' ' . $value . '\'';
        $this->assertAttributeSame($value, 'redirectTo', new $controller(), $msg);

        $name = 'VerificationController';
        $controller = '\\App\\Http\\Controllers\\Auth\\' . $name;
        $msg = 'The redirectTo property in the  should be set to \' ' . $value . '\'';
        $this->assertAttributeSame($value, 'redirectTo', new $controller(), $msg);
    }
}
