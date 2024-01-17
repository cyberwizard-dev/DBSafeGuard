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

        // Ask the user for the backup path
        $backupPath = $this->ask('Enter the backup path (default: storage/app/backups)', 'storage/app/backups');

        // Ensure the directory exists
        File::ensureDirectoryExists($backupPath);
        $backupFilePath = $backupPath . '/' . date('Y-m-d_H-i-s') . '_backup.sql';

        // Build the mysqldump command
        $command = "mysqldump -u{$username} -p{$password} {$database} > {$backupFilePath}";

        // Execute the mysqldump command
        exec($command);

        $this->info('Database backup completed successfully.');
    }

}
