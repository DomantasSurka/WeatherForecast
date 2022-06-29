# Weather Recommendations Application

## Description
Create a service, which returns product recommendations depending on the weather forecast.
- Products are stored in the database.
- Integrated third-party LHMT API (for specific day, output is created by the most occuring weather type).
- Request and response handled in JSON format.
- Caches are used for requests (for 5 min).
- Unit tests.
<br/>

## Challenges
- Find out about the structures and how to use Laravel framework.
- Fix many exceptions caused by Composer and PHP, when setting up the project.
- Laravel's configs (70% of the time fighting with them) :D 
<br/>
 

## Setup guide
- Download or clone repository.
- Open up the project.
- Make sure you have Composer and PHP installed.
- Update dependencies using composer through terminal:
```
composer update
```
- Connect to Database (turn on MySQL).
- Create database for laravel and tests:
```
php artisan db:create laravel
php artisan db:create test
```
- Execute migrations to set up products table:
```
php artisan migrate
```
- Add default products data to products table:
```
php artisan db:addData
```
- Launch the project:
```
php artisan serve
```
- Copy to browser's URL: (Kaunas can be replaced with another city)
```
http://127.0.0.1:8000/api/products/recommended/kaunas
```
<br/>

## How it works?
- User uses an adress with a specific city, to access product recommendations.
- Page displays recommendations for the next 3 days, including:
   - Weather forecast,
   - Day's date,
   - Two products, which are recommended for the weather, including:
     - SKU value,
     - Name value,
     - Price value.
- Recommended products are generated randomly.
- Products can be suitable for more than 1 weather type.
