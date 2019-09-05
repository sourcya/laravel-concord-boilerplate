### Sourcya Laravel Boilerplate

#### About the Project
> Sourcya Laravel boilerplate developed by [Sourcya](https://sourcya.com) developers
> depending on [Concord Package](https://github.com/artkonekt/concord) modular concept, so this is also a [Concord Box](https://artkonekt.github.io/concord/#/boxes)

Installation

- Install fresh Laravel app on your web server
- Add Sourcya folder to your app root directory
- Save the .env.example to .env
- Update the .env file with your db credentials
- Add Sourcya boilerplate and their associated modules repositories to your laravel composer.json
``` 
"repositories": [
        {
            "type": "path",
            "url": "sourcya/core",
            "options": {
                "symlink": true
            }
        },
        {
            "type": "path",
            "url": "sourcya/app-settings-module",
            "options": {
                "symlink": true
            }
        },
        {
            "type": "path",
            "url": "sourcya/address-module",
            "options": {
                "symlink": true
            }
        },
        {
            "type": "path",
            "url": "sourcya/contact-module",
            "options": {
                "symlink": true
            }
        },
        {
            "type": "path",
            "url": "sourcya/status-module",
            "options": {
                "symlink": true
            }
        },
        {
            "type": "path",
            "url": "sourcya/rating-module",
            "options": {
                "symlink": true
            }
        },
        {
            "type": "path",
            "url": "sourcya/attribute-module",
            "options": {
                "symlink": true
            }
        },
        {
            "type": "path",
            "url": "sourcya/boilerplate",
            "options": {
                "symlink": true
            }
        },
        {
            "type": "path",
            "url": "sourcya/notification-module",
            "options": {
                "symlink": true
            }
        },
        {
            "type": "path",
            "url": "sourcya/user-module",
            "options": {
                "symlink": true
            }
        },
        {
            "type": "path",
            "url": "sourcya/upload-module",
            "options": {
                "symlink": true
            }
        },
        {
            "type": "path",
            "url": "sourcya/agent-module",
            "options": {
                "symlink": true
            }
        },
        {
            "type": "path",
            "url": "sourcya/status-module",
            "options": {
                "symlink": true
            }
        }
    ],
    "require": {
        "sourcya/boilerplate": "dev-master",
    },
```
- Run: composer update
- Run: touch config/concord.php

>Edit config/concord.php and add your boxes service providers:
```
<?php

return [
    'modules' => [
          Sourcya\BoilerplateBox\Providers\ModuleServiceProvider::class => [],
          Sourcya\CoreBox\Providers\ModuleServiceProvider::class => []
           ]
];
```
- Run: php artisan sourcya:install
- Run: php artisan serve
