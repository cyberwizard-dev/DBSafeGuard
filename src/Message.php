<?php

namespace Dbsafeguard;

use Illuminate\Console\Command;

class Message extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dbsafeguard:post-install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Post-installation message for DBSafeguard package';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->info("This plugin was created by Cyberwizard \n\n Configuration");
        $this->info('The package uses environment variables to connect to the database and S3.');
        $this->comment('Database Variables:');
        $this->line('- DB_CONNECTION (default: mysql)');
        $this->line('- DB_DATABASE');
        $this->line('- DB_USERNAME');
        $this->line('- DB_PASSWORD');
        $this->info('');
        $this->comment('S3 Variables (if using --disk=s3):');
        $this->line('- AWS_ACCESS_KEY_ID');
        $this->line('- AWS_SECRET_ACCESS_KEY');
        $this->line('- AWS_DEFAULT_REGION');
        $this->line('- AWS_BUCKET');
        $this->info('');
        $this->info('Ensure these variables are correctly set in your .env file.');
    }
}
