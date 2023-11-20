<?php

namespace Dbsafeguard\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class BackupDatabase extends Command
{

    protected $signature = 'backup:database';
    protected $description = 'Backup the database';

    public function handle(): void
    {
        $database = env('DB_DATABASE');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');

        $backupDirectory = storage_path('app/backups');

        File::ensureDirectoryExists($backupDirectory);

        $backupFilePath = $backupDirectory . '/' . date('Y-m-d_H-i-s') . '_backup.sql';
        $command = "mysqldump -u{$username} -p{$password} {$database} > {$backupFilePath}";
        exec($command);
        $this->info('Database backup completed successfully.');
    }


}
