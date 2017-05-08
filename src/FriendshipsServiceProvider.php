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
        $stub      = __DIR__ . '/migrations/';
        $target    = database_path('migrations') . '/';
        $this->publishes([
            $stub . 'create_friendships_table.php'        => $target . date('Y_m_d_His', time()) . '_create_friendships_table.php',
        ], 'migrations');
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
