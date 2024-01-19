## Download https://github.com/Dahlia-Technologies-Pvt-Ltd/mts_society
* composer update
* config/database.php
	- 'default' => env('DB_CONNECTION', 'sqlsrv'),
    - under `connection` add new connection
        'sqlsrvclone' => [
            'driver' => 'sqlsrv',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', ''),
            'port' => env('DB_PORT', ''),
            'database' => '',
            'username' => '',
            'password' => '',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
        ],
* config/cors.php
	- change "paths"
	- change "allowed_origins"
* .env
	- APP_NAME
	- APP_ENV
	- APP_URL
	- DB_HOST
	- DB_DATABASE
	- DB_USERNAME
	- DB_PASSWORD
	- SMS_USERID
	* replace this variables
	- CB_BASE_URL = https://maharajaapptest-test.chargebee.com/api/v2/
	- CB_SITE_URL= https://maharajaapptest-test.chargebee.com/
	- API_TOKEN= test_4ahRMdp8UcuApDxLcuhd7TqcuKz6WLHnfYe
	- APP_DOMAIN=sandbox

	```
	DB_HOST_S=localhost
	DB_DATABASE_S=maharaja_master
	DB_USERNAME_S=sa
	DB_PASSWORD_S=sa@123
	SITE_BASE_URL_S=http://127.0.0.1:8000/register
	```
	
	
## Server Setup Shoud be needed
* Allow Headers : GET,POST,PUT,PATCH,DELETE,OPTIONS
* Allow Custom Headers : business-id,table-id,language-id
	

## importing Database
* maharaja_master
* maharaja_client_db
* create a folder name as Database
	- Data,Backup 
	- maharaja_master
	- db_setting table data update

## Important Command (Run command to root directory)
```
php artisan optimize:clear
php artisan cache:clear
php artisan storage:link
php artisan schedule:run
```
	
# Login Chargbee https://app.chargebee.com/login
* Copy the url and setup it .env file
	- CB_BASE_URL = https://maharajaapptest-test.chargebee.com/api/v2/
	- CB_SITE_URL= https://maharajaapptest-test.chargebee.com/
	
	- setting->Configure Chargebee->API Keys and Webhooks -> copy the API Key
	- API_TOKEN= test_4ahRMdp8UcuApDxLcuhd7TqcuKz6WLHnfYe
	
* Create a Product Family
* Create a Plan 
* Entilements->Features
	- Create New Features
	- click on features  and choose plan and setup the plans=>Download https://github.com/Dahlia-Technologies-Pvt-Ltd/restaurantapp.git

* app/providers/SettingsServiceProvider->boot function inside line shoud be commented
* composer update
* config/database.php
	- change host name
* config/cors.php
	- change "paths"
	- change "allowed_origins"
* .env
	- APP_NAME
	- APP_ENV
	- APP_URL
	- DB_HOST
	- DB_DATABASE
	- DB_USERNAME
	- DB_PASSWORD
	- SMS_USERID
	- replace this variables
	- CB_BASE_URL = https://maharajaapptest-test.chargebee.com/api/v2/
	- CB_SITE_URL= https://maharajaapptest-test.chargebee.com/
	- API_TOKEN= test_4ahRMdp8UcuApDxLcuhd7TqcuKz6WLHnfYe
	- APP_DOMAIN=sandbox
	
```
	DB_HOST_S=localhost
	DB_DATABASE_S=maharaja_master
	DB_USERNAME_S=sa
	DB_PASSWORD_S=sa@123
	SITE_BASE_URL_S=http://127.0.0.1:8000/register
```
	
	
## Server Setup Shoud be needed
	Allow Headers : GET,POST,PUT,PATCH,DELETE,OPTIONS
	Allow Custom Headers : business-id,table-id,language-id
	

## importing Database
	maharaja_master
	maharaja_client_db
	create a folder -> Database->Data,Backup ->maharaja_master->db_setting table data update

## Important Command (Run command to root directory)
	php artisan optimize:clear
	php artisan cache:clear
	php artisan storage:link
	php artisan schedule:run
	
	


	

	
	
=>Login Chargbee https://app.chargebee.com/login
1-Copy the url and setup it .env file
	CB_BASE_URL = https://maharajaapptest-test.chargebee.com/api/v2/
	CB_SITE_URL= https://maharajaapptest-test.chargebee.com/
	
	setting->Configure Chargebee->API Keys and Webhooks -> copy the API Key
	API_TOKEN= test_4ahRMdp8UcuApDxLcuhd7TqcuKz6WLHnfYe
	
2-Create a Product Family
3-Create a Plan 
4-Entilements->Features
	Create New Features
	click on features  and choose plan and setup the plans



<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/license.svg" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 1500 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Cubet Techno Labs](https://cubettech.com)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[Many](https://www.many.co.uk)**
- **[Webdock, Fast VPS Hosting](https://www.webdock.io/en)**
- **[DevSquad](https://devsquad.com)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

