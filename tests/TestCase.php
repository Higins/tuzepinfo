<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        $database = Config::get('database.connections.pgsql.database');

        try {
            Config::set('database.connections.pgsql.database', 'postgres');
            DB::purge('pgsql');
            DB::connection('pgsql')->getPdo();

            DB::statement("CREATE DATABASE {$database}");
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        Config::set('database.connections.pgsql.database', $database);
        DB::purge('pgsql');
        DB::connection('pgsql')->getPdo();

        return $app;
    }
}
