<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('lists', function () {
    return 'lists';
});

Route::group(['middleware' => []], function () {
    require base_path('routes/api/auth.php');
    require base_path('routes/api/user.php');
});
