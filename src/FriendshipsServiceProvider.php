<?php

namespace Merodiro\Friendships;

use Illuminate\Support\ServiceProvider;

class FriendshipsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {


        $this->publishes([
                __DIR__ . '/migrations' => $this->app->databasePath() . '/migrations'
            ], 'migrations');

        $this->loadMigrationsFrom(__DIR__.'/migrations');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
