<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/contracts/create', function () {
    return view('contracts.create');
})->middleware('auth');
