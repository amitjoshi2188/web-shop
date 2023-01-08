

Notes:
1. I have created migration files for customers,products,orders.
2. Added seeder file for customers and products, which feeds data based on csv file. on the customers csv file,
   for the column `registered_since` data is inaccurately given in the csv sheet in terms of name of days.
3. CRUD APIs are also created for orders & also created listing APIs for customers listing and product listing.
4. I have also attached postman collection file, after importing it, you can use all apis.
5. Passport package is used for APIs so except registration & login, every API needs token to run, which can be found on response of login API.
6. I have also created Basic test cases for PassportAuthController, which covers 100 % code coverage.


To run the project need to do below steps.
1. composer install		: to install dependency.
2. php artisan migrate  : to import tables structure.
3. php artisan db:seed --class=UserSeeder       : for creating default login details for dashboard (email : admin@admin.com, password : "password").
4. php artisan db:seed --class=ProductSeeder    : for import product csv data into database.
5. php artisan db:seed --class=CustomerSeeder   : for import Customers csv data into database.
6. php artisan passport:install					: to install passport dependency.
7. npm install
6. php artisan serve    : to start the project.
7. npm run dev`			: for starting npm package.

