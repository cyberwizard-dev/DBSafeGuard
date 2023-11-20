<?php

namespace Dbsafeguard;
class Message
{
    public static function postInstall(): void
    {
        echo "This plugin was created by Cyberwizard \n\n Configuration\n";
        echo "The package uses environment variables (DB_DATABASE, DB_USERNAME, DB_PASSWORD) to connect to the database. Ensure these variables are correctly set in your .env file.\n";
    }

}