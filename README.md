### Sourcya Laravel Boilerplate

#### About the Project
> Sourcya Laravel boilerplate developed by [Sourcya](https://sourcya.com) developers
> depending on [Concord Package](https://github.com/artkonekt/concord) modular concept, so this is also a [Concord Box](https://artkonekt.github.io/concord/#/boxes)

Installation

> Requires Composer and php7.*

- Install fresh Laravel app on your web server
```
$ composer create-project --prefer-dist laravel/laravel dev "5.8.*"
```
- Create a new MySql Database with charset UTF8mb4 (IMPORTANT)
> use phpmyadmin if you have it connected to your MySql/MariaDB host, or follow the following approach
```
//Connect to Mysql, if host is not on the same machine, add the host flag like this -h <ip_orHostName>
mysql -u <mysql username> -p

// After successful login you will have the mysql or mariadb terminal access
mysql> create database demo_smarty_dev_1 character set UTF8mb4 collate utf8mb4_unicode_ci;
```

- Update the .env file with your db credentials
- Install latest version of the package
```
$ composer require sourcya/boilerplate
```

> Edit config/concord.php and add your boxes service providers: (TODO: manage this file to be added by composer)
```
<?php

return [
    'modules' => [
          Sourcya\BoilerplateBox\Providers\ModuleServiceProvider::class => [],
          Sourcya\CoreBox\Providers\ModuleServiceProvider::class => []
           ]
];
```
- Change storage and cache folders permissions to 777
```
$ chmod 777 -R bootstrap/cache/
$ chmod 777 -R storage/
```
- Run: php artisan sourcya:install
- Run: php artisan serve
