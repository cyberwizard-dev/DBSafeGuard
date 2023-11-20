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
        $this->info('The package uses environment variables (DB_DATABASE, DB_USERNAME, DB_PASSWORD) to connect to the database. Ensure these variables are correctly set in your .env file.');
    }
}
