<?php

namespace Dbsafeguard;

use Illuminate\Support\ServiceProvider;
use packages\dbsafeguard\src\Console\Commands\BackupDatabase;

class BackupServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->commands([
            BackupDatabase::class,
        ]);
    }
}
