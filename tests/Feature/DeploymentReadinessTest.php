<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class DeploymentReadinessTest extends TestCase
{
    public function test_environment_configuration_is_set()
    {
        $this->assertNotEmpty(config('app.name'));
        $this->assertNotEmpty(config('app.env'));
        $this->assertNotEmpty(config('database.default'));
    }

    public function test_storage_directories_are_writable()
    {
        $this->assertTrue(is_writable(storage_path()));
        $this->assertTrue(is_writable(storage_path('framework/views')));
        $this->assertTrue(is_writable(storage_path('logs')));
    }

    public function test_queue_and_cache_drivers_are_configured()
    {
        $this->assertNotNull(config('queue.default'));
        $this->assertNotNull(config('cache.default'));
    }

    public function test_backup_database_command_executes_successfully()
    {
        $exitCode = Artisan::call('backup:database');
        $this->assertEquals(0, $exitCode);
    }

    public function test_clean_temporary_files_command_executes_successfully()
    {
        $exitCode = Artisan::call('storage:clean-temp');
        $this->assertEquals(0, $exitCode);
    }
}
