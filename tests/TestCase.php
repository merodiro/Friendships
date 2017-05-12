<?php

use Merodiro\Friendships\FriendShipsServiceProvider;
use GrahamCampbell\TestBench\AbstractPackageTestCase;
use Orchestra\Database\ConsoleServiceProvider;

abstract class TestCase extends AbstractPackageTestCase
{
    protected function getPackageProviders($app)
    {
        return [
        	FriendShipsServiceProvider::class,
        	ConsoleServiceProvider::class
        ];
    }

    protected function getEnvironmentSetUp($app)
	{
	    $app['config']->set('database.default', 'testbench');
	    $app['config']->set('database.connections.testbench', [
	        'driver'   => 'sqlite',
	        'database' => ':memory:',
	        'prefix'   => '',
	    ]);
	}

    public function setUp()
    {
        parent::setUp();

        $this->loadMigrationsFrom([
		    '--database' => 'testbench',
		    '--realpath' => realpath(__DIR__.'/database/migrations'),
		]);
		$this->loadMigrationsFrom([
			'--database' => 'testbench',
			'--realpath' => realpath(__DIR__.'/../src/migrations'),
		]);

        $this->withFactories(realpath(__DIR__.'/database/factories'));
    }
}