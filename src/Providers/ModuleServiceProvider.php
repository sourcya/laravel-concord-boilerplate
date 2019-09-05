<?php

namespace Sourcya\BoilerplateBox\Providers;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Konekt\Concord\BaseBoxServiceProvider;
use Sourcya\BoilerplateBox\Exceptions\SpatieHandler;
use Sourcya\BoilerplateBox\Helpers\SourcyaHelper;
use Sourcya\BoilerplateBox\Commands\SourcyaInstall;
use Illuminate\Support\Facades\Schema;

class ModuleServiceProvider extends BaseBoxServiceProvider
{
    public function boot()
    {
        parent::boot();

        Relation::morphMap([
            'Agent' => 'Sourcya\AgentModule\Models\Agent',
            'User' => 'Sourcya\UserModule\Models\User',
        ]);

        $this->app->singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            SpatieHandler::class
        );

        // In ModuleServiceProviders:
        $this->concord->registerHelper('sourcya_helper', SourcyaHelper::class);

        Collection::macro('paginate', function($perPage, $total = null, $page = null, $pageName = 'page') {
            $page = $page ?: LengthAwarePaginator::resolveCurrentPage($pageName);
            return new LengthAwarePaginator(
                $this->forPage($page, $perPage),
                $total ?: $this->count(),
                $perPage,
                $page,
                [
                    'path' => LengthAwarePaginator::resolveCurrentPath(),
                    'pageName' => $pageName,
                ]
            );
        });
    }

    public function register()
    {
        parent::register();
        Schema::defaultStringLength(191);

        $this->registerCommands();
    }

    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                SourcyaInstall::class,
            ]);
        }
    }
}
