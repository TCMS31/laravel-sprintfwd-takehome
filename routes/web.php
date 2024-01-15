<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This project is a JSON API; the only web route is the landing page. All
| application endpoints live in routes/api.php.
|
*/

Route::get('/', fn () => view('welcome'));
