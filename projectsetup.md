## project setup(Laravel react)
 1.change your environment variables to the latest php -v(eg.C:\wamp64\bin\php\php8.2.0)
 2.update the composer using command ----composer update
 3.run command  ------composer create-project --prefer-dist laravel/laravel mts_society-------- to create a new laravel project 
 4.then open in your editor and run ----php artisan serve----- to check whether the localhost is working properly or not.
 5.git clone the url to enter into your branch 
 6.make changes in .env file in your project

 ## .env(changes)
    1.APP_URL=http://localhost:8000
    2.DB_CONNECTION=sqlsrv
      DB_HOST=LAPTOP-RLPE4VM9(your computer name which can be seen in mssql)
      DB_PORT=
      DB_DATABASE=mts_society
      DB_USERNAME=sa(your login of mssql)
      DB_PASSWORD=sa@123(your password of mssql)
    3.comment # MAIL_MAILER=smtp
              # MAIL_HOST=mailpit
              # MAIL_PORT=1025
              # MAIL_USERNAME=null
              # MAIL_PASSWORD=null
              # MAIL_ENCRYPTION=null
              # MAIL_FROM_ADDRESS="hello@example.com"
              # MAIL_FROM_NAME="${APP_NAME}"

    4. and add 
         MAIL_MAILER=smtp
         MAIL_HOST=smtp.office365.com
         MAIL_PORT=587
         MAIL_USERNAME=testing@dahlia.tech
         MAIL_PASSWORD=tldyrjjpwbdvzljl
         MAIL_ENCRYPTION=tls
         MAIL_FROM_ADDRESS=testing@dahlia.tech
         MAIL_FROM_NAME="${APP_NAME}"
         MAIL_SSL=Yes
         MAIL_DEFAULT=Yes

## database.php file in config
1.'default' => env('DB_CONNECTION', 'sqlsrv')
2.'sqlsrv' => [
            'driver' => 'sqlsrv',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST','LAPTOP-RLPE4VM9'),
            'port' => env('DB_PORT'),
            'database' => env('DB_DATABASE', 'mts_society'),
            'username' => env('DB_USERNAME', 'sa'),
            'password' => env('DB_PASSWORD', 'sa@123'),
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
        ],
## add cache file in bootstrap file.
## add vendor file for the composer.
    