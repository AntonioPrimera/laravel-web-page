<?php

namespace AntonioPrimera\WebPage\Tests;

use AntonioPrimera\WebPage\WebPageServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

class TestCase extends \Orchestra\Testbench\TestCase
{
	/**
	 * Override this and provide a list of:
	 * [ 'path/to/migration/file1.php.stub' => 'MigrationClass1', ... ]
	 *
	 * @var array
	 */
	protected $migrate = [
		//__DIR__ . '/../database/migrations/2021_11_16_000000_create_components_table.php' => 'CreateComponentsTable',
		//__DIR__ . '/TestContext/migrations/create_users_table.php.stub' => 'CreateUsersTable',
		//__DIR__ . '/../database/migrations/add_role_to_users_table.php.stub' => 'AddRoleToUsersTable',
	];
	
	//protected function setUp(): void
	//{
	//	parent::setUp();
	//}
	
	protected function getPackageProviders($app)
	{
		return [
			WebPageServiceProvider::class,
		];
	}

	protected function getEnvironmentSetUp($app)
	{
		if ($this->migrate)
			$this->runPackageMigrations();
	}

	//--- Protected helpers -------------------------------------------------------------------------------------------

	protected function runPackageMigrations()
	{
		//this will reset the database
		Artisan::call('migrate:fresh');

		//import all migration files
		foreach ($this->migrate as $migrationFile => $migrationClass) {
			include_once $migrationFile;
			(new $migrationClass)->up();
		}
	}
}