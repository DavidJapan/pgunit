<?php

namespace Tests\Unit;

use Tests\TestCase;

class StandardViewsTest extends TestCase {

    public function testTables() {
        $filename = resource_path('views/tables');
        $this->assertTrue(file_exists($filename), 'The tables directory doesn\'t exist in the views directory');
        if (file_exists($filename)) {
            $filename = resource_path('views/tables/data-table.blade.php');
            $this->assertTrue(file_exists($filename), 'The data-table blade doesn\'t exist in the tables directory');
            $filename = resource_path('views/tables/single-editable-table.blade.php');
            $this->assertTrue(file_exists($filename), 'The single-editable-table blade doesn\'t exist in the tables directory');
        }
    }

    public function testElements() {
        $filename = resource_path('views/elements');
        $this->assertTrue(file_exists($filename), 'The elements directory doesn\'t exist in the views directory');
    }

    public function testLayouts() {
        $filename = resource_path('views/layouts');
        $this->assertTrue(file_exists($filename), 'The layouts directory doesn\'t exist in the views directory');
    }


}
