## Download https://github.com/Dahlia-Technologies-Pvt-Ltd/mts_society
* Copy Package1.json to Package.json and run
* Copy  resources/src/index 1.tsx and rename to resources/src/index.tsx
	- Uncomment this line {/*<BrowserRouter basename="/mts_society/public">*/} and set correct path.
* composer update
* npm install
* .env : Change following Variables
	- APP_NAME
	- APP_ENV
	- APP_URL
	- DB_HOST
	- DB_DATABASE
	- DB_USERNAME
	- DB_PASSWORD
	- MAIL_HOST
	- MAIL_PORT
	- MAIL_USERNAME
	- MAIL_PASSWORD

	* Custom variables
	- VITE_API_URL=http://localhost:8000
	- VITE_APP_URL=http://localhost:8000
	- APP_URL=http://localhost:8000
	- ASSET_URL=http://localhost:8000
	- APP_BASE_PATH=http://localhost:8000

	```
	DB_HOST_S=localhost
	DB_DATABASE_S=mts_society
	DB_USERNAME_S=sa
	DB_PASSWORD_S=sa@123
	SITE_BASE_URL_S=http://127.0.0.1:8000/register
	```
* config/database.php
	- 'default' => env('DB_CONNECTION', 'sqlsrv'),
    - under `connection` add new connection
	```
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
	```
* config/cors.php
	- change "paths"
	- change "allowed_origins"
	
## Server Setup Shoud be needed
* Allow Headers : GET,POST,PUT,PATCH,DELETE,OPTIONS
* Allow Custom Headers : business-id,table-id,language-id
	
## Importing Database
* Create a database with name `mts_society` and run command
	- php artisan migrate --database="sqlsrv" --path="database/migrations/society_master"

## Important Command (Run command to root directory)
```
php artisan optimize:clear
php artisan cache:clear
php artisan storage:link
php artisan schedule:run
```