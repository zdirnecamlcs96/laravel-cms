
# Summary

This package is a simple CMS with admin panel.

# Get Started

1. Run command `php artisan adminlte:install` to publish adminlte assests.

2. Run command `php artisan migrate` to setup admin database structure.

3. INclude `Admin` auth provider in `auth.php`.

```php
'providers' => [
    // ...
        'admins' => [
            'driver' => 'eloquent',
            'model' => Local\CMS\Models\Admin::class,
        ],
    // ...
],
```

4. Copy `menu` config from `local/cms/config/adminlte.php` to `config/adminlte.php`.

5. Done.