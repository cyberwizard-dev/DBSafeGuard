<?php

namespace Dbsafeguard\Providers;

use Dbsafeguard\Console\Commands\BackupDatabase;
use Dbsafeguard\Message;
use Illuminate\Support\ServiceProvider;

class BackupServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->commands([
            BackupDatabase::class,
            Message::class,
        ]);
    }
}
