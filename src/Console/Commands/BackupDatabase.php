<?php

namespace Dbsafeguard\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class BackupDatabase extends Command
{
    protected $signature = 'backup:db {--disk=local : The storage disk to use (local or s3)}';
    protected $description = 'Backup the database (MySQL/PostgreSQL) to a local path or S3. 
                                For S3, ensure you have set: 
                                AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_DEFAULT_REGION, AWS_BUCKET';

    public function handle(): void
    {
        $connection = config('database.default', env('DB_CONNECTION', 'mysql'));
        $disk = $this->option('disk');

        if ($connection === 'pgsql') {
            $database = config('database.connections.pgsql.database') ?? env('DB_DATABASE');
            $username = config('database.connections.pgsql.username') ?? env('DB_USERNAME');
            $password = config('database.connections.pgsql.password') ?? env('DB_PASSWORD');
            $host = config('database.connections.pgsql.host') ?? env('DB_HOST', '127.0.0.1');
            $port = config('database.connections.pgsql.port') ?? env('DB_PORT', '5432');
        } else {
            $database = config('database.connections.mysql.database') ?? env('DB_DATABASE');
            $username = config('database.connections.mysql.username') ?? env('DB_USERNAME');
            $password = config('database.connections.mysql.password') ?? env('DB_PASSWORD');
        }

        if ($disk === 's3') {
            $this->info('Starting S3 backup...');
            $this->comment('Required S3 Env Data:');
            $this->line('- AWS_ACCESS_KEY_ID');
            $this->line('- AWS_SECRET_ACCESS_KEY');
            $this->line('- AWS_DEFAULT_REGION');
            $this->line('- AWS_BUCKET');
        }

        $backupPath = storage_path('app/backups');
        if ($disk === 'local') {
            $backupPath = $this->ask('Enter the backup path', $backupPath);
        }

        File::ensureDirectoryExists($backupPath);
        $fileName = date('Y-m-d_H-i-s') . '_backup.sql';
        $backupFilePath = $backupPath . '/' . $fileName;

        $this->info("Dumping database to {$backupFilePath}...");
        
        if ($connection === 'pgsql') {
            $command = "PGPASSWORD=\"{$password}\" pg_dump -U {$username} -h {$host} -p {$port} {$database} > {$backupFilePath}";
        } else {
            $command = "mysqldump -u{$username} -p{$password} {$database} > {$backupFilePath}";
        }
        
        $result = null;
        $output = [];
        exec($command, $output, $result);

        if ($result !== 0) {
            $tool = $connection === 'pgsql' ? 'pg_dump' : 'mysqldump';
            $this->error("Database dump failed. Ensure {$tool} is installed and credentials are correct.");
            return;
        }

        if ($disk === 's3') {
            $this->info("Uploading to S3...");
            try {
                $fileContent = File::get($backupFilePath);
                \Illuminate\Support\Facades\Storage::disk('s3')->put('backups/' . $fileName, $fileContent);
                File::delete($backupFilePath);
                $this->info('Database backup uploaded to S3 and local temporary file removed.');
            } catch (\Exception $e) {
                $this->error('S3 upload failed: ' . $e->getMessage());
                return;
            }
        } else {
            $this->info('Database backup completed successfully at: ' . $backupFilePath);
        }
    }
}
