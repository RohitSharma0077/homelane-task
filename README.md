Steps to run a project
1. Clone the project 
2. Make new file ".env" and copy all the data of .env.example file to it
3. Run, composer install to generate vendor folder (if require run, composer update and composer autoload-dump)
4. php artisan migrate 
6. php artisan db:seed --class=superadmin_seeder 
5. http://localhost:80/homelane-task/public/index.php/