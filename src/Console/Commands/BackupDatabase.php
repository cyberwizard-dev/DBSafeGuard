<?php

namespace Dbsafeguard\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class BackupDatabase extends Command
{
    protected $signature = 'backup:db';
    protected $description = 'Backup the database';

    public function handle(): void
    {
        $database = env('DB_DATABASE');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');

        $backupPath = $this->ask('Enter the backup path (default: storage/app/backups)', 'storage/app/backups');

        File::ensureDirectoryExists($backupPath);
        $backupFilePath = $backupPath . '/' . date('Y-m-d_H-i-s') . '_backup.sql';
        $command = "mysqldump -u{$username} -p{$password} {$database} > {$backupFilePath}";
        exec($command);
        $this->info('Database backup completed successfully.');
    }

}
