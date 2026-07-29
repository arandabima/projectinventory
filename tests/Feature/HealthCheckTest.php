<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    public function test_login_routes_are_registered(): void
    {
        $this->assertTrue(Route::has('login'));
        $this->assertTrue(Route::has('admin.login'));
        $this->assertTrue(Route::has('auth.google.redirect'));
    }
}
