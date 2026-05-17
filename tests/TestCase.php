<?php

namespace Tests;

use App\Models\Setting;
use App\Support\AdminLoginPath;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\URL;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Setting::clearCache();
        AdminLoginPath::clearCache();
        URL::defaults([
            'adminLoginPath' => AdminLoginPath::DEFAULT_SEGMENT,
        ]);
    }
}
