<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(\App\Services\TasksService::class, \App\Services\TasksService::class );
        $this->app->singleton(\App\Services\MessagesService::class, \App\Services\MessagesService::class );
        $this->app->singleton(\App\Services\TaskRepository::class, \App\Services\TaskRepository::class );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
