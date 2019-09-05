<?php

return [
    'modules' => [
        Sourcya\NotificationModule\Providers\ModuleServiceProvider::class => [],
        Sourcya\UploadModule\Providers\ModuleServiceProvider::class => [],
        Sourcya\UserModule\Providers\ModuleServiceProvider::class => [],
        Sourcya\AgentModule\Providers\ModuleServiceProvider::class => [],
    ],

    'routes' => [
    'files'     => ['web', 'api'],
    //'namespace' => 'Name\\Space\\Here', // Defaults to the module's route namespace
    'prefix'    => null, // Defaults to the module's short name
    'as'        => null, // default is module's short name and a dot ('.') at the end
    'middleware'=> [] // defaults to ['web']
    ],

    'views' => [
    'namespace' => 'sourcya'
    ]

    ];
