<?php

namespace Tests\Unit;

use Tests\TestCase;

class StandardViewsJsCssTest extends TestCase {

    public function testStandardViewsJsCss() {
        $dirname = public_path('js/crud');
        $this->assertTrue(file_exists($dirname), $dirname . ' exists on the system.');
        if (file_exists($dirname)) {
            $files = [
                'namespace.js',
                'validation_rules.js',
                'get_scripts.js',
                'validation_rules.js',
                'leftmenu_ajaxhtml.js',
                'error_handler.js',
                'primary_key.js',
                'view_model_standard.js',
                'view_model_edit_users.js',
                'data_table_factory.js',
                'data_table_factory_extend.js',
                'template_binding_context.js',
                'data_table_factory_templates.js',
                'startup.js'
            ];
            foreach ($files as $file) {
                $filename = $dirname . '/' . $file;
                $this->assertTrue(file_exists($filename), $filename . ' doesn\'t exist');
            }
        }
        $dirname = public_path('js/vendor');
        $this->assertTrue(file_exists($dirname), $dirname . ' exists on the system.');
        if (file_exists($dirname)) {
            $files = [
                'app.js',
                'vendor.js'
            ];
            foreach ($files as $file) {
                $filename = $dirname . '/' . $file;
                $this->assertTrue(file_exists($filename), $filename . ' doesn\'t exist');
            }
        }
    }

}
