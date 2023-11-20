# DBSafeguard Database Backup

DBSafeguard Database Backup is a simple Laravel package that provides a convenient command-line interface to backup your database.

## Installation

1. Install the package via Composer:

```bash
composer require cyber/dbsafeguard
```

2. **Usage:**

To manually backup your database, run the following command:

```bash
php artisan backup:database
```

The backup file will be stored in the `storage/app/backups` directory with a timestamped filename.

3. **Schedule Backups:**

You can schedule automatic backups by adding the following entry to your Laravel task scheduler (usually defined in the `App\Console\Kernel` class):

```php
$schedule->command('backup:database')->daily();
```

This will run the database backup command daily. Adjust the schedule as needed. Here are some examples:

- `$schedule->command('backup:database')->daily();` - Run daily at midnight.
- `$schedule->command('backup:database')->dailyAt('3:00');` - Run daily at 3:00 AM.
- `$schedule->command('backup:database')->twiceDaily(1, 13);` - Run twice a day at 1:00 AM and 1:00 PM.
<!-- Add more examples as needed -->

## Configuration

The package uses environment variables (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`) to connect to the database. Ensure these variables are correctly set in your `.env` file.

## Author
Cyberwizard

Contact: [eminibest@gmail.com](mailto:eminibest@gmail.com)

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).