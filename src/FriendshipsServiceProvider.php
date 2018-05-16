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
            __DIR__ . '/config/friendships.php' => config_path('friendships.php'),
        ], 'config');

        $this->mergeConfigFrom(
            __DIR__ . '/config/friendships.php',
            'friendships'
        );

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
