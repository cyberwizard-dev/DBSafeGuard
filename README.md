# DBSafeguard Database Backup

DBSafeguard Database Backup is a simple Laravel package that provides a convenient command-line interface to backup your database.

## Installation

1. Install the package via Composer:

```bash
composer require cyberwizard/dbsafeguard:dev-main

```

2. **Usage:**

To manually backup your database, run the following command:

```bash
php artisan backup:db
```

The backup file will be stored in the `storage/app/backups` directory with a timestamped filename.

3. **Schedule Backups:**

You can schedule automatic backups by adding the following entry to your Laravel task scheduler (usually defined in the `App\Console\Kernel` class):

```php
$schedule->command('backup:db')->daily();
```

This will run the database backup command daily. Adjust the schedule as needed. Here are some examples:

- `$schedule->command('backup:db')->daily();` - Run daily at midnight.
- `$schedule->command('backup:db')->dailyAt('3:00');` - Run daily at 3:00 AM.
- `$schedule->command('backup:db')->twiceDaily(1, 13);` - Run twice a day at 1:00 AM and 1:00 PM.
<!-- Add more examples as needed -->

## Configuration

The package uses environment variables (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`) to connect to the database. Ensure these variables are correctly set in your `.env` file.



## Author
Cyberwizard

Contact: [eminibest@gmail.com](mailto:eminibest@gmail.com)

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).

## Extra

## Installation

You can install this package directly from the GitHub repository. Follow the steps below:

1. Open your project's `composer.json` file.

2. Add the GitHub repository to the `repositories` key:

    ```json
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/cyberwizard-dev/DBSafeGuard/"
        }
    ],
    ```

3. In the `require` section, specify the package name and version:

    ```json
    "require": {
        "cyberwizard/DBSafeGuard/": "dev-main"
    },
    ```

4. Run the following command to install the package:

    ```bash
    composer install
    ```

   This will fetch the package directly from the GitHub repository and install it in your project.

