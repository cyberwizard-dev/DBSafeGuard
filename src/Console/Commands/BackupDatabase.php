<?php

namespace Dbsafeguard\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

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
        
        $process = Process::fromShellCommandline($command);
        $process->setTimeout(null);
        
        $dumpBar = $this->output->createProgressBar();
        $dumpBar->start();
        
        $process->start();
        while ($process->isRunning()) {
            $dumpBar->advance();
            usleep(200000); // 200ms
        }
        
        $dumpBar->finish();
        $this->line('');

        if (!$process->isSuccessful()) {
            $tool = $connection === 'pgsql' ? 'pg_dump' : 'mysqldump';
            $this->error("Database dump failed. Ensure {$tool} is installed and credentials are correct. Error: " . $process->getErrorOutput());
            return;
        }

        if ($disk === 's3') {
            $this->info("Uploading to S3...");
            try {
                $fileSize = filesize($backupFilePath);
                $uploadBar = $this->output->createProgressBar($fileSize);
                $uploadBar->setFormat(' %current%/%max% bytes [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s%');
                $uploadBar->start();

                if (!in_array('upload_progress', stream_get_filters())) {
                    stream_filter_register('upload_progress', UploadProgressFilter::class);
                }

                UploadProgressFilter::$progressBar = $uploadBar;

                $stream = fopen($backupFilePath, 'r');
                stream_filter_append($stream, 'upload_progress');

                \Illuminate\Support\Facades\Storage::disk('s3')->put('backups/' . $fileName, $stream);
                
                if (is_resource($stream)) {
                    fclose($stream);
                }
                
                $uploadBar->finish();
                $this->line('');

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

class UploadProgressFilter extends \php_user_filter
{
    public static $progressBar;

    public function filter($in, $out, &$consumed, $closing): int
    {
        while ($bucket = stream_bucket_make_writeable($in)) {
            $consumed += $bucket->datalen;
            if (self::$progressBar) {
                self::$progressBar->advance($bucket->datalen);
            }
            stream_bucket_append($out, $bucket);
        }
        return PSFS_PASS_ON;
    }
}
