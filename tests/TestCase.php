<?php

namespace Tests;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $compiledPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'lead-crm-blade';

        if (! is_dir($compiledPath)) {
            mkdir($compiledPath, 0777, true);
        }

        $this->app['config']->set('view.compiled', $compiledPath);
    }
}
