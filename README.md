# DBSafeGuard

DBSafeGuard is a Laravel package that provides a convenient command-line interface to back up your database. It supports MySQL and PostgreSQL databases, and allows you to save backups locally or upload them directly to AWS S3.

## Requirements

- PHP 8.0 or higher
- Laravel 9.0, 10.0, 11.0, or 12.0+
- `mysqldump` or `pg_dump` installed on your server (depending on your database)

## Installation

Install the package via Composer:

```bash
composer require cyberwizard/dbsafeguard
```

If you are installing directly from the GitHub repository, add the following to your `composer.json` first:

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/cyberwizard-dev/DBSafeGuard"
    }
]
```
Then run `composer require cyberwizard/dbsafeguard:dev-main`.

## Configuration

The package automatically uses your standard Laravel database configuration. Ensure the standard environment variables are set in your `.env` file:

```env
DB_CONNECTION=mysql # or pgsql
DB_HOST=127.0.0.1
DB_PORT=3306 # or 5432
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### S3 Configuration

If you plan to upload your backups to AWS S3, you must configure your S3 credentials in your `.env` file:

```env
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_DEFAULT_REGION=your_region
AWS_BUCKET=your_bucket_name
```

## Usage

### Local Backup

To back up your database to the local disk, run:

```bash
php artisan backup:db
```

The command will prompt you for a backup path. If you accept the default, the backup file will be stored in the `storage/app/backups` directory with a timestamped filename.

### S3 Backup

To back up your database and upload it directly to S3, use the `--disk` option:

```bash
php artisan backup:db --disk=s3
```

This will create a temporary local dump, upload it to the `backups/` directory in your S3 bucket, and then remove the local temporary file.

### Scheduled Backups

You can schedule automatic backups using Laravel's task scheduler. Add the following to your console routes or `App\Console\Kernel` class:

```php
// Local backup daily at midnight
Schedule::command('backup:db')->daily();

// S3 backup twice daily
Schedule::command('backup:db --disk=s3')->twiceDaily(1, 13);
```

## Author

Cyberwizard  
Contact: [eminibest@gmail.com](mailto:eminibest@gmail.com)

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).
