<?php

use Merodiro\Friendships\FriendshipsServiceProvider;
use GrahamCampbell\TestBench\AbstractPackageTestCase;
use Orchestra\Database\ConsoleServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends AbstractPackageTestCase
{
    use RefreshDatabase;
    
    protected function getPackageProviders($app)
    {
        return [
            FriendshipsServiceProvider::class
        ];
    }

    public function setUp()
    {
        parent::setUp();

        $this->loadLaravelMigrations('sqlite');
        $this->withFactories(__DIR__.'/database/factories');
    }
}
