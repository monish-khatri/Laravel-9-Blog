## [Laravel - MultiTenant](https://spatie.be/docs/laravel-multitenancy/v1/introduction)

- `composer require spatie/laravel-multitenancy`
- `php artisan vendor:publish --provider="Spatie\Multitenancy\MultitenancyServiceProvider"`
- config/multitenancy.php
	```
    'tenant_finder' => DomainTenantFinder::class,
        'switch_tenant_tasks' => [
	        // \Spatie\Multitenancy\Tasks\PrefixCacheTask::class,
	        \Spatie\Multitenancy\Tasks\SwitchTenantDatabaseTask::class,
	        // \Spatie\Multitenancy\Tasks\SwitchRouteCacheTask::class,
	    ],
	'tenant_database_connection_name' => 'tenant',
 	'landlord_database_connection_name' => 'landlord',
    ```
- app\Http\Kernel.php
	```
    protected $middlewareGroups = [
		    'web' => [
		        // ...
		        \Spatie\Multitenancy\Http\Middleware\NeedsTenant::class,
		        \Spatie\Multitenancy\Http\Middleware\EnsureValidTenantSession::class,
		    ]
		];
	protected $middlewareGroups = [
		    // ...
		    'tenant' => [
		        \Spatie\Multitenancy\Http\Middleware\NeedsTenant::class,
		        \Spatie\Multitenancy\Http\Middleware\EnsureValidTenantSession::class,
		    ]
		];
    ```
- config/database.php
	```
    'connections' => [
        'tenant' => [
            'driver' => 'mysql',
            'database' =>  null, // Null cause we need to use dynamic database
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
        ],

        'landlord' => [
            'driver' => 'mysql',
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
    ],
    ```
-   `php artisan migrate --path=database/migrations/landlord --database=landlord`

- Add Data manually to `tenants` table to use dynamic database

- app/Providers/AppServiceProvider.php
    ```
	register()
    {
        //...
	    Passport::ignoreMigrations()
    }
    ```
- `php artisan tenants:artisan "migrate --database=tenant"`
- `php artisan tenants:artisan "migrate --database=tenant --seed"`
- add `connection` property to model files
	```
    protected $connection = "tenant";
    ```

- add `connection` in `config/auth.php`
	```
    'passwords' => [
	        'users' => [
	        	//...
	            'connection' => 'tenant',
	        ],
	    ],
    ```
