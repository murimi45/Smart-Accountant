<?php

namespace Tests;

use App\Http\Middleware\EnsureTwoFactorPassed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(EnsureTwoFactorPassed::class);
    }

    protected function beforeRefreshingDatabase(): void
    {
        $this->ensureTestDatabaseExists();
    }

    protected function ensureTestDatabaseExists(): void
    {
        if (config('database.default') !== 'mysql') {
            return;
        }

        $database = config('database.connections.mysql.database');
        if (! $database) {
            return;
        }

        $connection = config('database.connections.mysql');
        $connection['database'] = null;

        config(['database.connections.mysql_setup' => $connection]);
        DB::purge('mysql_setup');

        DB::connection('mysql_setup')->statement(
            'CREATE DATABASE IF NOT EXISTS `'.str_replace('`', '``', $database).'`'
        );

        DB::purge('mysql_setup');
    }

    protected function migrateFreshUsing(): array
    {
        return [
            '--path'     => database_path('migrations/testing'),
            '--realpath' => true,
        ];
    }

    protected function assertBlockedCrossTenant($response): void
    {
        $this->assertContains(
            $response->status(),
            [403, 404],
            'Expected cross-tenant request to be blocked with 403 or 404, got '.$response->status()
        );
    }
}
